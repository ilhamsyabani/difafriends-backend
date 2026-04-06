<?php

namespace App\Actions\Fortify;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    use ProfileValidationRules;

    public function update(User $user, array $input): void
    {
        validator($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'      => ['nullable', 'string', 'max:20'],
            'city'       => ['nullable', 'string', 'max:100'],
            'bio'        => ['nullable', 'string', 'max:1000'],
            'photo'      => ['nullable', 'image', 'max:2048'],
        ])->validate();

        // Handle photo upload
        if (isset($input['photo'])) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $input['photo']->store('photos', 'public');
            $user->photo = $path;
        }

        $user->forceFill([
            'first_name' => $input['first_name'],
            'last_name'  => $input['last_name'],
            'email'      => $input['email'],
            'phone'      => $input['phone'] ?? null,
            'city'       => $input['city'] ?? null,
            'bio'        => $input['bio'] ?? null,
        ])->save();
    }
}
