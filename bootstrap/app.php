<?php

use App\Exceptions\AjaxException;
use App\Exceptions\ApiException;
use App\Exceptions\ZaloException;
use App\Http\Middleware\CheckFeatureAccess;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Session\Middleware\AuthenticateSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            AuthenticateSession::class,
        ]);
         $middleware->alias([
             'checkFeature' => CheckFeatureAccess::class
         ]);

        $middleware->validateCsrfTokens(except: [
            '/zalo-chatbot',
        ]);
        $middleware->validateCsrfTokens(except: ['*']);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //Return Json API Exception
        $exceptions->render(function (ApiException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json($e->context(), $e->getCode());
            }
        });

        $exceptions->render(function (AjaxException $e, Request $request) {
            return response()->json($e->context(), $e->getCode());
        });

        $exceptions->report(function (ZaloException $e) {
            Log::channel('zalo')->error($e->getMessage(), [
                'code' => $e->getCode(),
                'previous' => $e->getPrevious(),
            ]);
            return false;
        });
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('app:refresh-zalo-access-token')->monthlyOn(1, '00:00');
        $schedule->command('app:refresh-zalo-access-token')->monthlyOn(15, '00:00');
        $schedule->command('app:refresh-facebook-user-access-token')->monthlyOn(2, '00:00');
        $schedule->command('app:refresh-facebook-user-access-token')->monthlyOn(16, '00:00');
    })
    ->create();
