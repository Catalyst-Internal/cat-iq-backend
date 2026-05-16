<?php

namespace App\Services;

use App\Models\Milestone;
use App\Models\Repository;
use Illuminate\Support\Collection;

class GitHubCacheShaper
{
    /**
     * @return array<string, mixed>|list<mixed>
     */
    public function shape(string $cacheKey): array
    {
        if ($cacheKey === 'rollout') {
            return $this->shapeRollout();
        }

        if (preg_match('/^tier\.(.+)\.status$/', $cacheKey, $matches)) {
            return $this->shapeTierStatus($matches[1]);
        }

        return match ($cacheKey) {
            'timeline' => [
                'tiers' => $this->timelineTiers(),
                'integration_milestone' => config('catiq.integration_milestone'),
            ],
            'milestones' => $this->shapeMilestones(),
            'activity' => [],
            default => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function shapeRollout(): array
    {
        $tiers = [];

        foreach (config('catiq.tiers', []) as $slug => $repoName) {
            $tiers[] = $this->tierRolloutSlice($slug, $repoName);
        }

        return [
            'integration_milestone' => config('catiq.integration_milestone'),
            'tiers' => $tiers,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function tierRolloutSlice(string $slug, string $repoName): array
    {
        $repo = Repository::query()->where('name', $repoName)->first();
        $milestone = $repo
            ? Milestone::query()
                ->where('repository_id', $repo->id)
                ->where('state', 'open')
                ->orderBy('due_on')
                ->first()
            : null;

        $progress = $milestone?->progressPercent() ?? 0;

        return [
            'slug' => $slug,
            'label' => config("catiq.tier_labels.{$slug}", strtoupper(substr($slug, 0, 2))),
            'progress_percent' => $progress,
            'current_phase' => $this->phaseFromProgress($progress),
            'completed_phases' => $this->completedPhases($progress),
            'current_focus' => $milestone?->title ?? '',
            'next_milestone' => $milestone ? [
                'title' => $milestone->title,
                'due' => $milestone->due_on?->toDateString() ?? '',
            ] : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function shapeTierStatus(string $slug): array
    {
        if (! array_key_exists($slug, config('catiq.tiers', []))) {
            return [];
        }

        $repoName = config("catiq.tiers.{$slug}");
        $repo = Repository::query()->where('name', $repoName)->first();
        $milestone = $repo
            ? Milestone::query()
                ->where('repository_id', $repo->id)
                ->where('state', 'open')
                ->orderBy('due_on')
                ->first()
            : null;

        $progress = $milestone?->progressPercent() ?? 0;

        return [
            'slug' => $slug,
            'progress_percent' => $progress,
            'current_phase' => $this->phaseFromProgress($progress),
            'completed_phases' => $this->completedPhases($progress),
            'current_focus' => $milestone?->title ?? '',
            'next_milestone' => $milestone ? [
                'id' => $milestone->github_id,
                'title' => $milestone->title,
                'due' => $milestone->due_on?->toDateString() ?? '',
                'percent_complete' => $progress,
            ] : null,
            'active_issues' => [],
            'open_issue_count' => $repo?->statusSnapshot?->open_issues ?? 0,
            'recent_prs' => [],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function shapeMilestones(): array
    {
        return Milestone::query()
            ->with('repository')
            ->orderBy('due_on')
            ->get()
            ->map(function (Milestone $milestone): array {
                $slug = $this->slugForRepo($milestone->repository?->name);

                return [
                    'id' => $milestone->github_id,
                    'tier_slug' => $slug ?? '',
                    'tier_label' => $slug ? config("catiq.tier_labels.{$slug}", '') : '',
                    'title' => $milestone->title,
                    'key' => '',
                    'due' => $milestone->due_on?->toDateString() ?? '',
                    'status' => $milestone->state === 'closed' ? 'DONE' : 'IN-PROGRESS',
                    'percent' => $milestone->progressPercent(),
                    'open_issues' => $milestone->open_issues,
                    'closed_issues' => $milestone->closed_issues,
                    'linked_issues' => [],
                    'url' => '',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function timelineTiers(): array
    {
        $tiers = [];

        foreach (config('catiq.tiers', []) as $slug => $repoName) {
            $repo = Repository::query()->where('name', $repoName)->first();
            $milestones = $repo
                ? Milestone::query()->where('repository_id', $repo->id)->orderBy('due_on')->get()
                : new Collection;

            $tiers[] = [
                'slug' => $slug,
                'label' => config("catiq.tier_labels.{$slug}", ''),
                'milestones' => $milestones->map(fn (Milestone $m) => [
                    'id' => $m->github_id,
                    'key' => '',
                    'title' => $m->title,
                    'due' => $m->due_on?->toDateString() ?? '',
                    'status' => $m->state === 'closed' ? 'DONE' : 'IN-PROGRESS',
                    'percent' => $m->progressPercent(),
                ])->values()->all(),
            ];
        }

        return $tiers;
    }

    private function slugForRepo(?string $repoName): ?string
    {
        if ($repoName === null) {
            return null;
        }

        foreach (config('catiq.tiers', []) as $slug => $name) {
            if ($name === $repoName) {
                return $slug;
            }
        }

        return null;
    }

    private function phaseFromProgress(int $progress): string
    {
        return match (true) {
            $progress >= 90 => 'LIVE',
            $progress >= 60 => 'BETA',
            $progress >= 30 => 'MVP',
            default => 'PROTOTYPE',
        };
    }

    /**
     * @return list<string>
     */
    private function completedPhases(int $progress): array
    {
        $phases = ['PROTOTYPE', 'MVP', 'BETA', 'LIVE'];
        $completed = [];

        foreach ($phases as $phase) {
            if ($this->phaseFromProgress($progress) === $phase) {
                break;
            }
            $completed[] = $phase;
        }

        return $completed;
    }
}
