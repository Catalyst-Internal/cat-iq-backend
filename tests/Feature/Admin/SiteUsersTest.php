<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SiteUsersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('dashboard.auth_user', 'test');
        Config::set('dashboard.auth_password', 'secret');
    }

    public function test_site_users_requires_basic_auth(): void
    {
        $this->get('/admin/site-users')->assertStatus(401);
    }

    public function test_site_users_lists_users_with_last_login(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@thelyst.com',
        ]);

        UserActivityLog::query()->create([
            'user_id' => $user->id,
            'event' => 'login',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'created_at' => now()->subHour(),
        ]);

        $response = $this->withBasicAuth('test', 'secret')->get('/admin/site-users');

        $response->assertOk();
        $response->assertSee('Test User');
        $response->assertSee('test@thelyst.com');
        $response->assertSee('thelyst.com');
    }
}
