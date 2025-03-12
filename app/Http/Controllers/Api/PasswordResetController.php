<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Mengirim OTP untuk proses reset password
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem kami.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->authService->sendOTP($request->email);

        if (!$result['success']) {
            return back()
                ->with('error', $result['message'])
                ->withInput();
        }

        // Simpan email di session untuk halaman verifikasi
        session(['email' => $request->email]);

        return redirect()->route('login.verify-otp.form')
            ->with('success', 'Kami telah mengirimkan kode OTP ke email Anda.');
    }

    /**
     * Verifikasi OTP
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOTP(Request $request)
    {
        // Gabungkan digit OTP menjadi satu string
        $otp = '';
        for ($i = 1; $i <= 6; $i++) {
            $otp .= $request->input('otp_digit_' . $i, '');
        }

        $request->merge(['otp' => $otp]);

        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $email = session('email');

        if (!$email) {
            return redirect()->route('login')
                ->with('error', 'Sesi telah berakhir. Silakan ulangi proses reset password.');
        }

        // Verifikasi OTP
        $verifyResult = $this->authService->verifyOTP($email, $request->otp);

        if (!$verifyResult['success']) {
            return back()
                ->with('error', $verifyResult['message'])
                ->withInput();
        }

        // Reset password
        $resetResult = $this->authService->resetPassword(
            $email,
            $verifyResult['reset_token'],
            $request->password
        );

        if (!$resetResult['success']) {
            return back()
                ->with('error', $resetResult['message'])
                ->withInput();
        }

        // Hapus session
        session()->forget('email');

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /**
     * Kirim ulang OTP
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendOTP(Request $request)
    {
        $email = session('email');

        if (!$email) {
            return redirect()->route('login')
                ->with('error', 'Sesi telah berakhir. Silakan ulangi proses reset password.');
        }

        $result = $this->authService->resendOTP($email);

        if (!$result['success']) {
            return back()
                ->with('error', $result['message']);
        }

        return back()
            ->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}
