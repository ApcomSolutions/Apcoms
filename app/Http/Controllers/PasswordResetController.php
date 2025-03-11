<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class PasswordResetController extends Controller
{
    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        
        // Generate OTP (misalnya 6 digit)
        $otp = mt_rand(100000, 999999);
        
        // Simpan OTP ke database
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10); // OTP berlaku 10 menit
        $user->save();
        
        // Kirim OTP ke email
        Mail::to($user->email)->send(new OtpMail($otp));
        
        return back()->with('status', 'We have sent an OTP to your email address.');
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        
        // Gabungkan semua digit OTP
        $otp = '';
        for ($i = 1; $i <= 6; $i++) {
            $digit = $request->input('otp_digit_' . $i);
            if (!$digit) {
                return back()->withErrors(['otp' => 'Please enter all 6 digits of the OTP.']);
            }
            $otp .= $digit;
        }
        
        $user = User::where('email', $request->email)
                    ->where('otp', $otp)
                    ->where('otp_expires_at', '>', now())
                    ->first();
        
        if (!$user) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }
        
        // Jika OTP valid, arahkan ke halaman reset password
        return redirect()->route('login.reset-password.form', ['token' => encrypt($user->id)]);
    }

    public function resendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        // Generate OTP baru
        $otp = mt_rand(100000, 999999);
        
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();
        
        // Kirim OTP baru
        Mail::to($user->email)->send(new OtpMail($otp));
        
        return back()->with([
            'status' => 'A new verification code has been sent to your email.',
            'email' => $request->email
        ]);
    }
}