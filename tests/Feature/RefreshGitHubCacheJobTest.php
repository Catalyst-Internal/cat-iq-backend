<?php

namespace Tests\Feature;

use App\Jobs\RefreshGitHubCacheJob;
use App\Models\GitHubCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefreshGitHubCacheJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_refresh_job_populates_rollout_cache_key(): void
    {
        $this->assertNull(GitHubCache::query()->where('cache_key', 'rollout')->first());

        RefreshGitHubCacheJob::dispatchSync();

        $row = GitHubCache::query()->where('cache_key', 'rollout')->first();

        $this->assertNotNull($row);
        $this->assertArrayHasKey('tiers', $row->payload);
        $this->assertNotNull(GitHubCache::query()->where('cache_key', 'sync')->first());
    }
}
