<?php

namespace App\Providers;

use App\Http\Controllers\Cp\UserActivityLogController;
use App\Listeners\LogUserActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Statamic\Facades\CP\Nav;
use Statamic\Statamic;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Login::class, [LogUserActivity::class, 'handleLogin']);
        Event::listen(Registered::class, [LogUserActivity::class, 'handleRegistered']);
        Event::listen(Logout::class, [LogUserActivity::class, 'handleLogout']);

        Statamic::booted(function () {
            Statamic::pushCpRoutes(function () {
                Route::get('activity-logs', [UserActivityLogController::class, 'index'])
                    ->name('activity-logs.index');
            });

            Nav::extend(function () {
                Nav::users('Activity Logs')
                    ->route('activity-logs.index')
                    ->icon('calendar')
                    ->can('access cp');
            });
        });
    }
}
