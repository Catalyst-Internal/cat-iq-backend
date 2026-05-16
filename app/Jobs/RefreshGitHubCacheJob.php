<?php

namespace App\Jobs;

use App\Services\GitHubCacheService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RefreshGitHubCacheJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly ?string $cacheKey = null,
    ) {}

    public function handle(GitHubCacheService $cache): void
    {
        $cache->refresh($this->cacheKey);
    }
}
