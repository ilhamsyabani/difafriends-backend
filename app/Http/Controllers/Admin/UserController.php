<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Exports\UsersTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Models\User;
use App\Notifications\AccountCredentialsNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $users = User::when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        })
            ->when($request->sort, function ($query, $sort) use ($request) {
                $direction = $request->direction === 'desc' ? 'desc' : 'asc';
                $query->orderBy($sort, $direction);
            })
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    /**
     * Unduh template Excel kosong untuk impor pengguna.
     */
    public function template(): BinaryFileResponse
    {
        return Excel::download(new UsersTemplateExport, 'template-import-pengguna.xlsx');
    }

    /**
     * Impor pengguna dari file Excel, lalu kirim email kredensial ke tiap akun baru.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $import = new UsersImport;
        Excel::import($import, $request->file('file'));

        // Kirim kredensial ke setiap user yang berhasil dibuat (notifikasi di-queue).
        foreach ($import->created as $row) {
            $row['user']->notify(new AccountCredentialsNotification($row['password']));
        }

        $createdCount = count($import->created);

        if ($createdCount === 0 && empty($import->errors)) {
            return back()->with('error', 'Tidak ada data pengguna yang ditemukan pada file.');
        }

        $redirect = back();

        if ($createdCount > 0) {
            $redirect->with('success', "{$createdCount} pengguna berhasil diimpor dan email kredensial telah dikirim.");
        }

        if (! empty($import->errors)) {
            $redirect->with('error', count($import->errors).' baris dilewati: '.implode(' | ', $import->errors));
        }

        return $redirect;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/users/Form', ['user' => null]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,instructor,companion,admin',
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // role dan is_active tidak ada di $fillable — set via forceFill
        $role = Roles::from($validated['role']);
        $isActive = $validated['is_active'] ?? true;
        unset($validated['role'], $validated['is_active']);

        $user = User::create($validated);
        $user->forceFill(['role' => $role, 'is_active' => $isActive])->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User successfully created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('admin/users/Form', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:user,instructor,companion,admin',
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        // role dan is_active hanya boleh diubah oleh admin via forceFill
        $role = Roles::from($validated['role']);
        $isActive = $validated['is_active'] ?? $user->is_active;
        unset($validated['role'], $validated['is_active']);

        $user->update($validated);
        $user->forceFill(['role' => $role, 'is_active' => $isActive])->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Kirim ulang email kredensial ke pengguna yang dipilih.
     */
    public function resendCredentials(User $user): RedirectResponse
    {
        $plainPassword = Str::password(
            length: 10,
            letters: true,
            numbers: true,
            symbols: false,
        );

        $user->forceFill(['password' => Hash::make($plainPassword)])->save();

        $user->notify(new AccountCredentialsNotification($plainPassword));

        return back()->with('success', "Email kredensial berhasil dikirim ulang ke {$user->email}.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User has been successfully deleted.');
    }
}
