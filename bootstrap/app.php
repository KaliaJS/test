<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        apiPrefix: '',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            \App\Http\Middleware\AlwaysReturnJson::class,
        ]);
        $middleware->alias([
            'sanctum.optional' => \App\Http\Middleware\OptionalSanctumAuth::class,
            'auth.sensor' => \App\Http\Middleware\SensorTokenMiddleware::class,
        ]);
    })
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => '', 'middleware' => ['api', 'sanctum.optional']],
    )
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('sanctum:prune-expired --hours=24')->dailyAt('03:00');
        $schedule->command('rewards:expire')->dailyAt('04:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
