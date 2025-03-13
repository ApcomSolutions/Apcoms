<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        // Pastikan tidak ada kode yang menggunakan OtpMail di sini
        // Misalnya: Mail::to($request->email)->send(new OtpMail($otp));

        $result = $this->authService->sendOTP($request->email);

        if (!$result['success']) {
            return back()
                ->with('error', $result['message'])
                ->withInput();
        }

        return back()
            ->with('status', 'Kami telah mengirimkan link dan kode OTP ke email Anda.');
    }

    /**
     * Menampilkan form verifikasi OTP
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showVerifyOtpForm(Request $request)
    {
        $email = $request->email;
        $token = $request->token;

        if (!$email || !$token) {
            return redirect()->route('login')
                ->with('error', 'Link reset password tidak valid.');
        }

        return view('login.verify-otp', [
            'email' => $email,
            'token' => $token
        ]);
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
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verifikasi OTP dan token
        $verifyResult = $this->authService->verifyOTP($request->email, $request->otp);

        if (!$verifyResult['success']) {
            return back()
                ->with('error', $verifyResult['message'])
                ->withInput();
        }

        // Reset password dengan token hasil verifikasi
        $resetResult = $this->authService->resetPassword(
            $request->email,
            $verifyResult['reset_token'],
            $request->password
        );

        if (!$resetResult['success']) {
            return back()
                ->with('error', $resetResult['message'])
                ->withInput();
        }

        // Redirect ke halaman login dengan pesan sukses
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->authService->resendOTP($request->email);

        if (!$result['success']) {
            return back()
                ->with('error', $result['message']);
        }

        return back()
            ->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}
