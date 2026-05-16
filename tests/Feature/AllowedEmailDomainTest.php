<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AllowedEmailDomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_is_open_when_allowlist_empty(): void
    {
        Config::set('catiq.allowed_email_domains', []);

        $response = $this->post('/register', [
            'name' => 'Open User',
            'email' => 'user@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertNoContent();
        $this->assertAuthenticated();
    }

    public function test_register_allows_configured_domain(): void
    {
        Config::set('catiq.allowed_email_domains', ['thelyst.com']);

        $response = $this->post('/register', [
            'name' => 'Allowed User',
            'email' => 'user@thelyst.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertNoContent();
        $this->assertAuthenticated();
    }

    public function test_register_blocks_disallowed_domain(): void
    {
        Config::set('catiq.allowed_email_domains', ['thelyst.com']);

        $response = $this->postJson('/register', [
            'name' => 'Blocked User',
            'email' => 'user@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_blocks_disallowed_domain(): void
    {
        Config::set('catiq.allowed_email_domains', ['thelyst.com']);

        User::factory()->create([
            'email' => 'user@gmail.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/login', [
            'email' => 'user@gmail.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
        $this->assertGuest();
    }
}
