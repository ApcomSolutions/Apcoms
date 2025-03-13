<?php

namespace App\Services;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\OtpMail;

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
     * Kirim link reset password dan OTP ke email
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

            // Generate token untuk link reset password
            $resetToken = Str::random(64);

            // Simpan OTP dan token ke database
            DB::table('password_reset_tokens')
                ->updateOrInsert(
                    ['email' => $email],
                    [
                        'token' => $resetToken, // Token untuk link
                        'otp' => Hash::make($otp), // OTP untuk verifikasi
                        'created_at' => Carbon::now(),
                        'expires_at' => $expiresAt
                    ]
                );

            // Update user dengan OTP
            $user->otp = $otp;
            $user->otp_expires_at = $expiresAt;
            $user->save();

            // Buat URL reset
            $resetUrl = url('/verify-otp?email=' . urlencode($email) . '&token=' . $resetToken);

            // Kirim email dengan OTP dan link reset
            Mail::send('emails.reset-password-otp', [
                'otp' => $otp,
                'resetUrl' => $resetUrl
            ], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Kode Verifikasi Reset Password');
            });

            return [
                'success' => true,
                'message' => 'Link reset password dan OTP berhasil dikirim',
                'email' => $email
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengirim OTP: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verifikasi token dari link dan OTP yang dimasukkan user
     *
     * @param string $email
     * @param string $token
     * @param string $otp
     * @return array
     */
    public function verifyOTP(string $email, string $otp): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !$user->otp || Carbon::parse($user->otp_expires_at)->isPast()) {
            return [
                'success' => false,
                'message' => 'OTP tidak valid atau sudah kadaluarsa'
            ];
        }

        if ($user->otp !== $otp) {
            return [
                'success' => false,
                'message' => 'OTP tidak valid'
            ];
        }

        // Ambil data reset token dari database
        $resetData = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetData || Carbon::parse($resetData->expires_at)->isPast()) {
            return [
                'success' => false,
                'message' => 'Token reset password tidak valid atau sudah kadaluarsa'
            ];
        }

        // Generate reset token untuk reset password
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
     * Validasi token dari link reset password
     *
     * @param string $email
     * @param string $token
     * @return array
     */
    public function validateResetToken(string $email, string $token): array
    {
        $resetData = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$resetData || Carbon::parse($resetData->expires_at)->isPast()) {
            return [
                'success' => false,
                'message' => 'Link reset password tidak valid atau sudah kadaluarsa'
            ];
        }

        return [
            'success' => true,
            'message' => 'Token valid'
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
        $user->otp = null;
        $user->otp_expires_at = null;
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
        // Kirim OTP baru
        return $this->sendOTP($email);
    }
}
