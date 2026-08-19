<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'store_owner' => \App\Http\Middleware\EnsureUserIsStoreOwner::class,
        ]);

        // Trust forwarded headers from reverse proxies (ngrok, load balancers, etc.)
        // so the app sees HTTPS/Host as the browser sees them. In production, replace
        // '*' with your real proxy IPs to avoid clients spoofing their scheme/host.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
