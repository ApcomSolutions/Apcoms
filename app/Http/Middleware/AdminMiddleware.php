<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            return redirect()->route('login');
        }

        // Cek apakah user memiliki role admin
        $user = Auth::user();
        if (!$user->is_admin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. Admin access required.'
                ], 403);
            }
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akses ditolak. Anda bukan admin.');
        }

        // Tambahkan SEO data untuk halaman admin dengan noindex
        view()->share('seoData', new SEOData(
            title: 'Admin Panel - ApCom Solutions',
            description: 'Panel administrasi internal ApCom Solutions',
            robots: 'noindex,nofollow' // Melarang mesin pencari mengindeks halaman admin
        ));

        return $next($request);
    }
}
