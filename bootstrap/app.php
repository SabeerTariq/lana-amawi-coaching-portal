<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'subscription.limits' => \App\Http\Middleware\CheckSubscriptionLimits::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Redirect to homepage when route is not found (e.g., when session expires and tries to redirect to non-existent 'login' route)
        $exceptions->render(function (\Symfony\Component\Routing\Exception\RouteNotFoundException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Route not found'], 404);
            }
            return redirect()->route('booking');
        });
    })->create();
