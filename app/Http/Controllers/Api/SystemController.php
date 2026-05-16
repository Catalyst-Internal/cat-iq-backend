<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\RespondsWithApiEnvelope;
use App\Http\Controllers\Controller;
use App\Services\GitHubCacheService;
use Illuminate\Http\JsonResponse;

class SystemController extends Controller
{
    use RespondsWithApiEnvelope;

    public function __construct(
        private readonly GitHubCacheService $cache,
    ) {}

    public function sync(): JsonResponse
    {
        $last = $this->cache->lastSyncAt();

        return $this->envelope([
            'last_sync_at' => $last?->toIso8601String(),
            'relative' => $last?->diffForHumans() ?? '',
        ]);
    }
}
