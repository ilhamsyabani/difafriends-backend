<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\ScalevWelcomeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ScalevWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        // ── 1. Verifikasi secret ───────────────────────────────────────────────
        // Scalev mengirim secret via header X-Scalev-Secret.
        // Fail-closed: kalau secret tidak diset, tolak semua request.
        $secret = config('services.scalev.webhook_secret');

        if (empty($secret) || ! hash_equals($secret, (string) $request->header('X-Scalev-Secret', ''))) {
            Log::warning('Scalev webhook: invalid or missing secret');

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        // ── 2. Hanya proses event pembayaran lunas ─────────────────────────────
        if (($payload['payment_status'] ?? null) !== 'paid') {
            return response()->json(['message' => 'Ignored']);
        }

        // ── 3. Cari course berdasarkan scalev_product_id ───────────────────────
        $productId = $payload['product_id'] ?? null;

        if (! $productId) {
            Log::warning('Scalev webhook: missing product_id', $payload);

            return response()->json(['message' => 'Missing product_id'], 422);
        }

        $course = Course::where('scalev_product_id', $productId)->first();

        if (! $course) {
            Log::warning("Scalev webhook: course not found for product_id={$productId}");

            return response()->json(['message' => 'Course not found'], 404);
        }

        // ── 4. Ambil data customer ─────────────────────────────────────────────
        $customer = $payload['customer'] ?? [];
        $email = $customer['email'] ?? null;
        $name = $customer['name'] ?? 'User';

        if (! $email) {
            Log::warning('Scalev webhook: missing customer email', $payload);

            return response()->json(['message' => 'Missing customer email'], 422);
        }

        // ── 5. Cari atau buat user ─────────────────────────────────────────────
        $isNewUser = false;
        $plainPassword = null;

        $user = User::where('email', $email)->first();

        if (! $user) {
            $isNewUser = true;
            // Gunakan hanya alphanumeric untuk password agar tidak ada masalah
            // saat disalin dari email (hindari special characters yang bisa corrupt)
            $plainPassword = Str::password(
                length: 12,
                letters: true,
                numbers: true,
                symbols: false,
            );
            $nameParts = explode(' ', $name, 2);

            $user = new User([
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'email' => $email,
                'password' => Hash::make($plainPassword),
            ]);

            // Tandai email sebagai terverifikasi karena sudah confirmed via Scalev
            $user->email_verified_at = now();
            $user->save();

            $user->assignRole(Roles::User);
        }

        // ── 6. Buat enrollment (idempoten) ─────────────────────────────────────
        $alreadyEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if (! $alreadyEnrolled) {
            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'active',
                'enrolled_at' => now(),
            ]);
        }

        // ── 7. Kirim email kredensial jika user baru ───────────────────────────
        if ($isNewUser && $plainPassword) {
            $user->notify(new ScalevWelcomeNotification($plainPassword, $course));
        }

        Log::info("Scalev webhook: enrolled user={$user->email} course={$course->slug} new_user={$isNewUser}");

        return response()->json(['message' => 'OK']);
    }
}
