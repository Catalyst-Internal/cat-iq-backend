<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\RespondsWithApiEnvelope;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MetricsController extends Controller
{
    use RespondsWithApiEnvelope;

    public function index(): JsonResponse
    {
        return $this->envelope([], [
            'status' => 'pending',
        ]);
    }
}
