<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Habilitar el middleware de Sanctum para requests desde el frontend
        // Esto permite que Sanctum use sesiones en lugar de tokens API
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
        ]);

        // Aplicar el middleware de idioma globalmente a las rutas API
        $middleware->api(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Excluir rutas de autenticación de la validación CSRF para permitir testing con Postman
        $middleware->validateCsrfTokens(except: [
            'api/login',
            'api/register',
            'api/logout',
            'api/forgot-password',
            'api/reset-password',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
