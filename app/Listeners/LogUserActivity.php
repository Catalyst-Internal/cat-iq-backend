<?php

namespace App\Listeners;

use App\Models\UserActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;

class LogUserActivity
{
    public function handleLogin(Login $event): void
    {
        $this->write('login', $event->user);
    }

    public function handleRegistered(Registered $event): void
    {
        $this->write('register', $event->user);
    }

    public function handleLogout(Logout $event): void
    {
        $this->write('logout', $event->user);
    }

    private function write(string $event, ?Authenticatable $user): void
    {
        $request = request();

        UserActivityLog::query()->create([
            'user_id' => $user?->getAuthIdentifier(),
            'event' => $event,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
