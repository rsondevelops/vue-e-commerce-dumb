<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $exception, \Illuminate\Http\Request $request) {
            if ($response->getStatusCode() === 403 && $request->header('X-Inertia')) {
                return Inertia::render('Errors/403', [
                    'message' => $exception->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.',
                ])->toResponse($request)->setStatusCode(403);
            }
            if ($response->getStatusCode() === 404 && $request->header('X-Inertia')) {
                return Inertia::render('Errors/404', [
                    'message' => 'Halaman tidak ada / Module dalam tahap pengembangan',
                ])->toResponse($request)->setStatusCode(404);
            }
            return $response;
        });
    })->create();