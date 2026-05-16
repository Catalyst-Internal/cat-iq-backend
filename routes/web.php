<?php

use App\Http\Controllers\Admin\SiteUsersController;
use App\Http\Controllers\GitHubWebhookController;
use App\Http\Middleware\BasicAuth;
use App\Http\Middleware\VerifyGitHubWebhook;
use App\Livewire\OrgOverview;
use App\Livewire\RepoDetail;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/github', [GitHubWebhookController::class, 'handle'])
    ->middleware(VerifyGitHubWebhook::class);

require __DIR__.'/auth.php';

Route::middleware([BasicAuth::class])->group(function () {
    Route::get('/', OrgOverview::class);
    Route::get('/repos/{repository:name}', RepoDetail::class);
    Route::get('/admin/site-users', [SiteUsersController::class, 'index'])->name('admin.site-users.index');
});
