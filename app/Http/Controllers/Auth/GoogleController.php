<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

class GoogleController extends Controller
{
    /**
     * Arahkan pengguna ke halaman persetujuan OAuth Google.
     */
    public function redirect(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback dari Google, lalu login atau daftarkan pengguna.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Sesi login Google kedaluwarsa. Silakan coba lagi.']);
        } catch (Throwable) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Gagal masuk dengan Google. Silakan coba lagi atau gunakan email & password.']);
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Google Anda tidak memiliki email yang dapat digunakan.']);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            $this->linkGoogleAccount($user, $googleUser);
        } else {
            $user = $this->createUserFromGoogle($googleUser, $email);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Tautkan google_id ke akun yang sudah ada (mis. user yang dulu daftar via email).
     */
    private function linkGoogleAccount(User $user, SocialiteUser $googleUser): void
    {
        $attributes = ['google_id' => $googleUser->getId()];

        if (! $user->email_verified_at) {
            $attributes['email_verified_at'] = now();
        }

        if (! $user->photo && $googleUser->getAvatar()) {
            $attributes['photo'] = $googleUser->getAvatar();
        }

        $user->forceFill($attributes)->save();
    }

    /**
     * Buat akun baru dari data profil Google. Email dari Google dianggap terverifikasi.
     */
    private function createUserFromGoogle(SocialiteUser $googleUser, string $email): User
    {
        $name = trim((string) $googleUser->getName());
        $parts = $name !== '' ? explode(' ', $name, 2) : [Str::before($email, '@')];

        $user = User::create([
            'first_name' => $parts[0],
            'last_name' => $parts[1] ?? '',
            'email' => $email,
            'google_id' => $googleUser->getId(),
            'photo' => $googleUser->getAvatar(),
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        return $user;
    }
}
