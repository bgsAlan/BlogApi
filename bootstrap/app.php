<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //add middleware
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class
        ]);
    })

->withExceptions(function (Exceptions $exceptions) {

    // 404 — Model tidak ditemukan (Route Model Binding gagal)
    $exceptions->render(function (ModelNotFoundException $e, $request) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }
    });

    // 404 — Route tidak ditemukan
    $exceptions->render(function (NotFoundHttpException $e, $request) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Endpoint tidak ditemukan.',
            ], 404);
        }
    });

    // 401 — Unauthenticated
    $exceptions->render(function (AuthenticationException $e, $request) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Kamu belum login. Silakan login terlebih dahulu.',
            ], 401);
        }
    });

    // 422 — Validation error (format udah bagus, tapi kita standardize)
    $exceptions->render(function (ValidationException $e, $request) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data yang dikirim tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        }
    });

    // HTTP Exception lain (403, 429, dll)
    $exceptions->render(function (HttpException $e, $request) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Terjadi kesalahan.',
            ], $e->getStatusCode());
        }
    });
})->create();
