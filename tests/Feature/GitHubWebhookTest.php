<?php

namespace Tests\Feature;

use App\Jobs\RefreshGitHubCacheJob;
use App\Jobs\SyncRoadmapJob;
use App\Jobs\SyncStatusJob;
use App\Jobs\SyncWikiJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class GitHubWebhookTest extends TestCase
{
    use RefreshDatabase;

    /**
     * GitHub signs the raw request body; use a literal POST body (not postJson re-encoding).
     *
     * @param  array<string, string>  $headers
     */
    private function postGitHubWebhookRaw(string $body, array $headers): TestResponse
    {
        $server = [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];
        foreach ($headers as $name => $value) {
            $server['HTTP_'.strtoupper(str_replace('-', '_', $name))] = $value;
        }

        return $this->call('POST', '/webhooks/github', [], [], [], $server, $body);
    }

    public function test_webhook_rejects_bad_signature(): void
    {
        $this->postJson('/webhooks/github', ['repository' => ['id' => 1]], [
            'X-GitHub-Event' => 'milestone',
            'X-Hub-Signature-256' => 'sha256=deadbeef',
        ])->assertStatus(403);
    }

    public function test_milestone_webhook_dispatches_sync_status(): void
    {
        Bus::fake();

        $body = json_encode([
            'action' => 'created',
            'repository' => [
                'id' => 4242,
                'name' => 'demo',
                'full_name' => 'catalyst-internal/demo',
                'default_branch' => 'main',
                'private' => false,
                'topics' => [],
            ],
        ], JSON_THROW_ON_ERROR);

        $secret = (string) config('github.webhook_secret');
        $signature = 'sha256='.hash_hmac('sha256', $body, $secret);

        $this->postGitHubWebhookRaw($body, [
            'X-GitHub-Event' => 'milestone',
            'X-Hub-Signature-256' => $signature,
        ])->assertOk()->assertJson(['status' => 'queued']);

        $this->assertDatabaseHas('repositories', [
            'github_id' => 4242,
            'name' => 'demo',
        ]);

        Bus::assertDispatched(SyncStatusJob::class);
        Bus::assertDispatched(RefreshGitHubCacheJob::class);

        $this->assertDatabaseHas('github_webhook_events', [
            'event' => 'milestone',
            'action' => 'created',
        ]);
    }

    public function test_webhook_logs_event_when_repository_missing_from_payload(): void
    {
        Bus::fake();

        $body = json_encode(['zen' => 'anything'], JSON_THROW_ON_ERROR);
        $secret = (string) config('github.webhook_secret');
        $signature = 'sha256='.hash_hmac('sha256', $body, $secret);

        $this->postGitHubWebhookRaw($body, [
            'X-GitHub-Event' => 'ping',
            'X-Hub-Signature-256' => $signature,
        ])->assertOk()->assertJson(['status' => 'queued']);

        Bus::assertDispatched(RefreshGitHubCacheJob::class);
        Bus::assertNotDispatched(SyncStatusJob::class);
        Bus::assertNotDispatched(SyncWikiJob::class);
        Bus::assertNotDispatched(SyncRoadmapJob::class);

        $this->assertDatabaseHas('github_webhook_events', [
            'event' => 'ping',
            'repository_id' => null,
        ]);
    }
}
