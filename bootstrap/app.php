<?php

use App\Http\Middleware\EnsureMemberEmailIsVerified;
use App\Http\Middleware\EnsureMemberIsApproved;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Csp\AddCspHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(AddCspHeaders::class);
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
        ]);

        $middleware->alias([
            'verified.member' => EnsureMemberEmailIsVerified::class,
            'approved.member' => EnsureMemberIsApproved::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
