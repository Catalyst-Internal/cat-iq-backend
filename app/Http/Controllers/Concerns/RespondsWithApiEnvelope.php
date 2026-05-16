<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;

trait RespondsWithApiEnvelope
{
    /**
     * @param  array<string, mixed>  $meta
     */
    protected function envelope(mixed $data, array $meta = []): JsonResponse
    {
        $body = ['data' => $data];

        if ($meta !== []) {
            $body['meta'] = $meta;
        }

        return response()->json($body);
    }

    protected function githubUnavailable(): JsonResponse
    {
        return response()->json([
            'message' => 'GitHub data unavailable. No cached payload.',
            'meta' => ['cached_at' => null],
        ], 503);
    }
}
