<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthService
{
    /**
     * Login user dan generate token
     *
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array
    {
        // Periksa apakah kredensial valid
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Email atau password salah'
            ];
        }

        $user = User::where('email', $credentials['email'])->first();

        // Periksa apakah user adalah admin
        if (!$user->is_admin) {
            return [
                'success' => false,
                'message' => 'Akun Anda tidak memiliki akses admin'
            ];
        }

        // Generate token untuk API
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'token' => $token,
            'user' => $user
        ];
    }

    /**
     * Logout user dan revoke token
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Kirim OTP ke email untuk reset password
     *
     * @param string $email
     * @return array
     */
    public function sendOTP(string $email): array
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Email tidak ditemukan'
                ];
            }

            // Cek apakah user adalah admin
            if (!$user->is_admin) {
                return [
                    'success' => false,
                    'message' => 'Akun ini tidak memiliki akses admin'
                ];
            }

            // Generate OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = Carbon::now()->addMinutes(10);

            // Simpan OTP ke database
            DB::table('password_reset_tokens')
                ->updateOrInsert(
                    ['email' => $email],
                    [
                        'token' => Hash::make($otp),
                        'created_at' => Carbon::now(),
                        'expires_at' => $expiresAt
                    ]
                );

            // Kirim email dengan OTP
            $this->sendOTPEmail($email, $otp);

            return [
                'success' => true,
                'message' => 'OTP berhasil dikirim'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengirim OTP: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verifikasi OTP yang dimasukkan user
     *
     * @param string $email
     * @param string $otp
     * @return array
     */
    public function verifyOTP(string $email, string $otp): array
    {
        $resetData = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetData) {
            return [
                'success' => false,
                'message' => 'OTP tidak valid atau sudah kadaluarsa'
            ];
        }

        if (Carbon::parse($resetData->expires_at)->isPast()) {
            return [
                'success' => false,
                'message' => 'OTP sudah kadaluarsa'
            ];
        }

        if (!Hash::check($otp, $resetData->token)) {
            return [
                'success' => false,
                'message' => 'OTP tidak valid'
            ];
        }

        // Generate reset token
        $resetToken = Str::random(60);

        // Update token
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update([
                'token' => Hash::make($resetToken),
                'expires_at' => Carbon::now()->addMinutes(30)
            ]);

        return [
            'success' => true,
            'reset_token' => $resetToken
        ];
    }

    /**
     * Reset password setelah verifikasi OTP
     *
     * @param string $email
     * @param string $resetToken
     * @param string $password
     * @return array
     */
    public function resetPassword(string $email, string $resetToken, string $password): array
    {
        $resetData = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetData) {
            return [
                'success' => false,
                'message' => 'Token reset password tidak valid'
            ];
        }

        if (Carbon::parse($resetData->expires_at)->isPast()) {
            return [
                'success' => false,
                'message' => 'Token reset password sudah kadaluarsa'
            ];
        }

        if (!Hash::check($resetToken, $resetData->token)) {
            return [
                'success' => false,
                'message' => 'Token reset password tidak valid'
            ];
        }

        // Update password
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($password);
        $user->save();

        // Hapus token reset
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        return [
            'success' => true,
            'message' => 'Password berhasil direset'
        ];
    }

    /**
     * Kirim ulang OTP
     *
     * @param string $email
     * @return array
     */
    public function resendOTP(string $email): array
    {
        // Hapus OTP lama jika ada
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        // Kirim OTP baru
        return $this->sendOTP($email);
    }

    /**
     * Kirim email OTP
     *
     * @param string $email
     * @param string $otp
     * @return void
     */
    private function sendOTPEmail(string $email, string $otp): void
    {
        // Implementasi menggunakan Laravel Mail
        // Bisa dibuat view email yang lebih menarik
        Mail::send('emails.reset-password-otp', ['otp' => $otp], function ($message) use ($email) {
            $message->to($email)
                ->subject('Kode Verifikasi Reset Password');
        });

        // Fallback jika view tidak ada
        if (count(Mail::failures()) > 0) {
            Mail::raw("Kode OTP untuk reset password Anda adalah: $otp. Kode ini berlaku selama 10 menit.", function ($message) use ($email) {
                $message->to($email)
                    ->subject('Reset Password OTP');
            });
        }
    }
}
