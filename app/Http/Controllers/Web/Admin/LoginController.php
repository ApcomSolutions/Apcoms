<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Menampilkan form login
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Jika sudah login, redirect ke dashboard admin
        if (Auth::check()) {
            return redirect()->route('admin.home');
        }

        return view('login.index');
    }

    /**
     * Handle login
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Cek apakah user ada dan memiliki role admin
        if (!$user || !$user->is_admin) {
            return back()
                ->withErrors(['email' => 'Email atau password salah atau akun Anda tidak memiliki akses admin.'])
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.home'));
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput();
    }

    /**
     * Handle logout
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
