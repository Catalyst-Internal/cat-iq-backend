<?php

namespace Tests\Feature;

use App\Models\GitHubCache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_is_available(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_github_rollout_requires_authentication(): void
    {
        $this->getJson('/api/v1/github/rollout')->assertUnauthorized();
    }

    public function test_github_rollout_returns_503_when_cache_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/v1/github/rollout')
            ->assertStatus(503)
            ->assertJsonPath('meta.cached_at', null);
    }

    public function test_github_rollout_returns_contract_envelope_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        GitHubCache::query()->create([
            'cache_key' => 'rollout',
            'payload' => [
                'integration_milestone' => '2026-07-01',
                'tiers' => [],
            ],
            'ttl_seconds' => 300,
            'fetched_at' => now(),
            'expires_at' => now()->addSeconds(300),
        ]);

        $this->actingAs($user)
            ->getJson('/api/v1/github/rollout')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['integration_milestone', 'tiers'],
                'meta' => ['cached_at', 'ttl'],
            ]);
    }

    public function test_content_home_returns_envelope(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/v1/content/home')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['hero', 'why', 'layers', 'impact'],
            ]);
    }

    public function test_system_sync_returns_last_sync_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/v1/system/sync')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['last_sync_at', 'relative'],
            ]);
    }
}
