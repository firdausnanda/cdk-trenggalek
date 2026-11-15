<?php

use App\Http\Middleware\ModuleTranslationMiddleware;
use App\Http\Middleware\SetlocaleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/auth.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        },
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust the reverse proxy / load balancer (e.g. Cloudflare, Nginx) so Laravel
        // sees the original https scheme from X-Forwarded-Proto.
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            SetlocaleMiddleware::class,
            ModuleTranslationMiddleware::class,
        ]);

        $middleware->api(prepend: [
            'throttle:60,1',
        ]);

        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo('/admin');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
