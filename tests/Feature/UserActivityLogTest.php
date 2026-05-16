<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_writes_activity_log(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertNoContent();

        $this->assertDatabaseHas('user_activity_logs', [
            'user_id' => $user->id,
            'event' => 'login',
        ]);
    }

    public function test_register_writes_activity_log(): void
    {
        $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNoContent();

        $user = User::query()->where('email', 'new@example.com')->first();
        $this->assertNotNull($user);

        $this->assertDatabaseHas('user_activity_logs', [
            'user_id' => $user->id,
            'event' => 'register',
        ]);
    }

    public function test_logout_writes_activity_log(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertNoContent();

        $this->assertDatabaseHas('user_activity_logs', [
            'user_id' => $user->id,
            'event' => 'logout',
        ]);
    }

    public function test_login_and_logout_events_are_distinct(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->actingAs($user)->post('/logout');

        $events = UserActivityLog::query()
            ->where('user_id', $user->id)
            ->pluck('event')
            ->all();

        $this->assertContains('login', $events);
        $this->assertContains('logout', $events);
    }
}
