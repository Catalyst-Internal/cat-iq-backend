<?php

namespace App\Services;

use App\Models\GitHubCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GitHubCacheService
{
    /** @var array<string, int> */
    public const KEY_TTLS = [
        'rollout' => 300,
        'tier.data-warehouse.status' => 300,
        'tier.claude-os.status' => 300,
        'tier.datalyst.status' => 300,
        'timeline' => 300,
        'milestones' => 300,
        'activity' => 60,
        'sync' => 300,
    ];

    public function __construct(
        private readonly GitHubCacheShaper $shaper,
    ) {}

    public function getRow(string $cacheKey): ?GitHubCache
    {
        return GitHubCache::query()->where('cache_key', $cacheKey)->first();
    }

    /**
     * @return array{payload: array, cached_at: string, ttl: int}
     */
    public function payloadForResponse(string $cacheKey): ?array
    {
        $row = $this->getRow($cacheKey);

        if ($row === null) {
            return null;
        }

        return [
            'payload' => $row->payload,
            'cached_at' => $row->fetched_at->toIso8601String(),
            'ttl' => $row->ttl_seconds,
        ];
    }

    public function put(string $cacheKey, array $payload, ?int $ttlSeconds = null): GitHubCache
    {
        $ttl = $ttlSeconds ?? self::KEY_TTLS[$cacheKey] ?? 300;
        $now = Carbon::now();

        return GitHubCache::query()->updateOrCreate(
            ['cache_key' => $cacheKey],
            [
                'payload' => $payload,
                'ttl_seconds' => $ttl,
                'fetched_at' => $now,
                'expires_at' => $now->copy()->addSeconds($ttl),
            ],
        );
    }

    public function refresh(?string $cacheKey = null): void
    {
        $keys = $cacheKey !== null
            ? [$cacheKey]
            : array_values(array_filter(
                array_keys(self::KEY_TTLS),
                fn (string $key) => $key !== 'sync',
            ));

        foreach ($keys as $key) {
            try {
                $payload = $this->shaper->shape($key);
                $this->put($key, $payload);
            } catch (\Throwable $e) {
                Log::warning('github_cache refresh failed', [
                    'key' => $key,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->putSyncTimestamp();
    }

    public function lastSyncAt(): ?Carbon
    {
        $row = $this->getRow('sync');

        if ($row !== null && ! empty($row->payload['last_sync_at'])) {
            return Carbon::parse($row->payload['last_sync_at']);
        }

        $max = GitHubCache::query()
            ->where('cache_key', '!=', 'sync')
            ->max('fetched_at');

        return $max ? Carbon::parse($max) : null;
    }

    private function putSyncTimestamp(): void
    {
        $latest = GitHubCache::query()
            ->where('cache_key', '!=', 'sync')
            ->max('fetched_at');

        $this->put('sync', [
            'last_sync_at' => $latest
                ? Carbon::parse($latest)->toIso8601String()
                : null,
        ]);
    }
}
