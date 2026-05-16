<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\RespondsWithApiEnvelope;
use App\Http\Controllers\Controller;
use App\Services\StatamicContentService;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller
{
    use RespondsWithApiEnvelope;

    public function __construct(
        private readonly StatamicContentService $content,
    ) {}

    public function home(): JsonResponse
    {
        $data = $this->content->home();

        if ($data === null) {
            return response()->json(['message' => 'Content not found.'], 404);
        }

        return $this->envelope($data);
    }

    public function tier(string $slug): JsonResponse
    {
        $data = $this->content->tier($slug);

        if ($data === null) {
            return response()->json(['message' => 'Tier not found.'], 404);
        }

        return $this->envelope($data);
    }

    public function roadmap(): JsonResponse
    {
        $data = $this->content->roadmap();

        if ($data === null) {
            return response()->json(['message' => 'Content not found.'], 404);
        }

        return $this->envelope($data);
    }

    public function impact(): JsonResponse
    {
        $data = $this->content->impact();

        if ($data === null) {
            return response()->json(['message' => 'Content not found.'], 404);
        }

        return $this->envelope($data);
    }
}
