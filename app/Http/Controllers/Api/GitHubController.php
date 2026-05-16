<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\RespondsWithApiEnvelope;
use App\Http\Controllers\Controller;
use App\Services\GitHubCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GitHubController extends Controller
{
    use RespondsWithApiEnvelope;

    public function __construct(
        private readonly GitHubCacheService $cache,
    ) {}

    public function rollout(): JsonResponse
    {
        return $this->fromCache('rollout');
    }

    public function tierStatus(string $slug): JsonResponse
    {
        if (! array_key_exists($slug, config('catiq.tiers', []))) {
            return response()->json(['message' => 'Tier not found.'], 404);
        }

        return $this->fromCache("tier.{$slug}.status");
    }

    public function timeline(): JsonResponse
    {
        return $this->fromCache('timeline');
    }

    public function milestones(Request $request): JsonResponse
    {
        $resolved = $this->cache->payloadForResponse('milestones');

        if ($resolved === null) {
            return $this->githubUnavailable();
        }

        $data = $resolved['payload'];

        if ($request->filled('tier')) {
            $data = array_values(array_filter(
                $data,
                fn (array $row) => ($row['tier_slug'] ?? '') === $request->string('tier')->toString(),
            ));
        }

        if ($request->filled('status') && $request->string('status')->toString() !== 'all') {
            $status = strtoupper(str_replace('-', '-', $request->string('status')->toString()));
            $map = [
                'done' => 'DONE',
                'in-progress' => 'IN-PROGRESS',
                'todo' => 'TODO',
            ];
            $want = $map[$request->string('status')->toString()] ?? $status;
            $data = array_values(array_filter(
                $data,
                fn (array $row) => ($row['status'] ?? '') === $want,
            ));
        }

        return $this->envelope($data, [
            'cached_at' => $resolved['cached_at'],
            'ttl' => $resolved['ttl'],
        ]);
    }

    public function activity(Request $request): JsonResponse
    {
        $resolved = $this->cache->payloadForResponse('activity');

        if ($resolved === null) {
            return $this->githubUnavailable();
        }

        $data = $resolved['payload'];
        $limit = min(50, max(1, (int) $request->input('limit', 20)));

        if ($request->filled('tier')) {
            $data = array_values(array_filter(
                $data,
                fn (array $row) => ($row['tier_slug'] ?? '') === $request->string('tier')->toString(),
            ));
        }

        return $this->envelope(array_slice($data, 0, $limit), [
            'cached_at' => $resolved['cached_at'],
            'ttl' => $resolved['ttl'],
        ]);
    }

    private function fromCache(string $cacheKey): JsonResponse
    {
        $resolved = $this->cache->payloadForResponse($cacheKey);

        if ($resolved === null) {
            return $this->githubUnavailable();
        }

        return $this->envelope($resolved['payload'], [
            'cached_at' => $resolved['cached_at'],
            'ttl' => $resolved['ttl'],
        ]);
    }
}
