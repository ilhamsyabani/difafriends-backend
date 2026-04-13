<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class LogoController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('settings/Logo', [
            'currentLogo' => SiteSetting::get('app_logo')
                ? Storage::disk('public')->url(SiteSetting::get('app_logo'))
                : null,
            'appName' => SiteSetting::get('app_name', config('app.name')),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'app_name' => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->hasFile('logo')) {
            $old = SiteSetting::get('app_logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('logo')->store('logos', 'public');
            SiteSetting::set('app_logo', $path);
        }

        if ($request->filled('app_name')) {
            SiteSetting::set('app_name', $request->input('app_name'));
        }

        return to_route('settings.logo.edit')->with('success', 'Logo berhasil diperbarui.');
    }

    public function destroy(): RedirectResponse
    {
        $old = SiteSetting::get('app_logo');
        if ($old) {
            Storage::disk('public')->delete($old);
            SiteSetting::set('app_logo', null);
        }

        return to_route('settings.logo.edit')->with('success', 'Logo berhasil dihapus.');
    }
}
