<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Ubah sintaks pendaftaran middleware
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'api.secure' => \App\Http\Middleware\ApiSecurityMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Tangani database query exceptions
        $exceptions->renderable(function (QueryException $e, $request) {
            // Log detail error untuk debugging
            Log::error('Database error occurred', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => auth()->id() ?? 'guest'
            ]);

            // Penting: Error API selalu kembalikan JSON, tidak peduli Accept header
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kendala pada database. Silakan coba lagi nanti.',
                    'error_type' => 'database_error'
                ], 500);
            }

            // AJAX request detection - important for JS fetch
            if ($request->ajax() || $request->expectsJson() ||
                $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kendala pada database. Silakan coba lagi nanti.',
                    'error_type' => 'database_error'
                ], 500);
            }

            // Regular web request - check for view first
            if (view()->exists('errors.database')) {
                return response()->view('errors.database', [
                    'message' => 'Terjadi kendala pada database. Silakan coba lagi nanti.'
                ], 500);
            }

            // Fallback to redirect
            return back()
                ->withInput()
                ->with('error', 'Terjadi kendala pada database. Silakan coba lagi nanti.');
        });

        // Handle other exceptions
        $exceptions->renderable(function (\Throwable $e, $request) {
            // Skip if already handled
            if ($e instanceof QueryException) {
                return;
            }

            // Skip in development mode
            if (!app()->isProduction()) {
                return;
            }

            // Log error
            Log::error('Application error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);

            // API requests always return JSON
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kendala pada server. Tim kami telah diberitahu.',
                    'error_type' => 'server_error'
                ], 500);
            }

            // AJAX requests
            if ($request->ajax() || $request->expectsJson() ||
                $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kendala pada server. Tim kami telah diberitahu.',
                    'error_type' => 'server_error'
                ], 500);
            }

            // Web requests
            if (view()->exists('errors.500')) {
                return response()->view('errors.500', [
                    'message' => 'Terjadi kendala pada server. Tim kami telah diberitahu.'
                ], 500);
            }

            return back()
                ->with('error', 'Terjadi kendala pada server. Tim kami telah diberitahu.');
        });
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('cleanup:temp-files')->dailyAt('01:00');
    })
    ->create();
