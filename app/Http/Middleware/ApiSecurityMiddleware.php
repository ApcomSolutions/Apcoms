<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class ApiSecurityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Di lingkungan development, izinkan semua request
        if (App::environment('local', 'development', 'testing')) {
            return $next($request);
        }

        // Jika request berasal dari aplikasi itu sendiri (ada session/referrer yang valid)
        if ($this->isInternalRequest($request)) {
            // Izinkan semua request dari aplikasi sendiri
            return $next($request);
        }

        // Untuk request eksternal, periksa API key
        if (!$this->hasValidApiKey($request)) {
            return response()->json(['message' => 'Invalid or missing API key'], 403);
        }

        // Tambahkan rate limiting untuk request eksternal
        if ($this->isRateLimited($request)) {
            return response()->json(['message' => 'Rate limit exceeded'], 429);
        }

        // Log aktivitas write operations
        if (!$request->isMethod('get')) {
            \Log::info('API Write Operation', [
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'source' => $request->header('X-API-Client') ?? 'external'
            ]);
        }

        return $next($request);
    }

    /**
     * Memeriksa apakah request berasal dari aplikasi yang sama
     */
    private function isInternalRequest(Request $request): bool
    {
        // Request yang sudah terotentikasi pasti dari aplikasi sendiri
        if (auth()->check()) {
            return true;
        }

        // Cek cookie session Laravel
        if ($request->hasCookie('laravel_session')) {
            return true;
        }

        // Cek CSRF token
        $token = $request->header('X-CSRF-TOKEN') ?? $request->input('_token');
        if ($token && csrf_token() === $token) {
            return true;
        }

        // Cek referrer (jika berasal dari domain yang sama)
        $referer = $request->header('referer');
        if ($referer) {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            if ($refererHost === $appHost) {
                return true;
            }
        }

        return false;
    }

    /**
     * Memvalidasi API key untuk request eksternal
     */
    private function hasValidApiKey(Request $request): bool
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return false;
        }

        $validKeys = config('app.api_keys', [
            env('API_KEY_WEB_APP', 'a1b2c3d4e5f6g7h8i9j0') => 'web_app',
            env('API_KEY_MOBILE_APP', 'j9i8h7g6f5e4d3c2b1a0') => 'mobile_app',
            env('API_KEY_PARTNER', 'z9y8x7w6v5u4t3s2r1q0') => 'partner_api',
        ]);

        if (isset($validKeys[$apiKey])) {
            $request->headers->set('X-API-Client', $validKeys[$apiKey]);
            return true;
        }

        return false;
    }

    /**
     * Memeriksa rate limiting untuk request eksternal
     */
    private function isRateLimited(Request $request): bool
    {
        $identifier = $request->header('X-API-Client') ?? $request->ip();
        $key = "api-limit:{$identifier}";

        $limit = match($request->header('X-API-Client')) {
            'web_app' => 100,
            'mobile_app' => 60,
            'partner_api' => 30,
            default => 20
        };

        $current = Cache::increment($key);
        if ($current === 1) {
            Cache::put($key, 1, now()->addMinute(1));
        }

        return $current > $limit;
    }
}
