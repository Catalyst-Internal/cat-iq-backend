<?php

use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\GitHubController;
use App\Http\Controllers\Api\MetricsController;
use App\Http\Controllers\Api\SystemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/content/home', [ContentController::class, 'home']);
    Route::get('/content/tier/{slug}', [ContentController::class, 'tier']);
    Route::get('/content/roadmap', [ContentController::class, 'roadmap']);
    Route::get('/content/impact', [ContentController::class, 'impact']);

    Route::get('/github/rollout', [GitHubController::class, 'rollout']);
    Route::get('/github/tier/{slug}/status', [GitHubController::class, 'tierStatus']);
    Route::get('/github/timeline', [GitHubController::class, 'timeline']);
    Route::get('/github/milestones', [GitHubController::class, 'milestones']);
    Route::get('/github/activity', [GitHubController::class, 'activity']);

    Route::get('/system/sync', [SystemController::class, 'sync']);
    Route::get('/metrics', [MetricsController::class, 'index']);
});
