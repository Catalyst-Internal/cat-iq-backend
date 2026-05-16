<?php

use App\Http\Middleware\EnsureAllowedEmailDomain;
use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'webhooks/github',
        ]);

        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => EnsureEmailIsVerified::class,
            'allowed.email.domain' => EnsureAllowedEmailDomain::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// PHP 8.5 deprecates PDO::MYSQL_ATTR_SSL_CA in Laravel's merged framework config.
// Skip merging framework defaults on 8.5+ so vendor config/database.php is not evaluated.
if (PHP_VERSION_ID >= 80500) {
    $app->dontMergeFrameworkConfiguration();
}

return $app;
