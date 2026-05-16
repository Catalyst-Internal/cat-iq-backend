<?php

namespace App\Services;

use Statamic\Facades\Entry;

class StatamicContentService
{
    public function home(): ?array
    {
        $entry = $this->entry('pages', 'home');

        if ($entry !== null) {
            return [
                'hero' => [
                    'headline' => (string) $entry->get('hero_headline', ''),
                    'subheadline' => (string) $entry->get('hero_subheadline', ''),
                    'cta_label' => (string) $entry->get('hero_cta_label', ''),
                    'cta_url' => (string) $entry->get('hero_cta_url', ''),
                    'secondary_label' => (string) $entry->get('hero_secondary_label', ''),
                    'secondary_url' => (string) $entry->get('hero_secondary_url', ''),
                ],
                'why' => [
                    'headline' => (string) $entry->get('why_headline', ''),
                    'body' => (string) $entry->get('why_body', ''),
                ],
                'layers' => [
                    'headline' => (string) $entry->get('layers_headline', ''),
                    'subheadline' => (string) $entry->get('layers_subheadline', ''),
                ],
                'impact' => [
                    'headline' => (string) $entry->get('impact_headline', ''),
                ],
            ];
        }

        return config('catiq-content.home');
    }

    public function tier(string $slug): ?array
    {
        if (! array_key_exists($slug, config('catiq.tiers', []))) {
            return null;
        }

        $entry = $this->entry('tiers', $slug);

        if ($entry !== null) {
            return [
                'tier_number' => (int) $entry->get('tier_number', 0),
                'title' => (string) $entry->get('title', ''),
                'subtitle' => (string) $entry->get('subtitle', ''),
                'description_long' => (string) $entry->get('description_long', ''),
                'outcome' => (string) $entry->get('outcome', ''),
                'anatomy_content' => $entry->get('anatomy_content'),
                'team' => collect($entry->get('team', []))->map(fn ($row) => [
                    'name' => $row['member_name'] ?? '',
                    'initials' => $row['member_initials'] ?? '',
                    'title' => $row['member_title'] ?? '',
                    'role_on_tier' => $row['role_on_tier'] ?? '',
                    'employment_type' => $row['employment_type'] ?? 'staff',
                    'vendor_name' => $row['vendor_name'] ?? null,
                ])->values()->all(),
                'github_repo' => (string) $entry->get('github_repo', config("catiq.tiers.{$slug}")),
                'prev_tier_slug' => $entry->get('prev_tier_slug'),
                'next_tier_slug' => $entry->get('next_tier_slug'),
                'seo_title' => $entry->get('seo_title'),
                'seo_description' => $entry->get('seo_description'),
            ];
        }

        return config("catiq-content.tiers.{$slug}");
    }

    public function roadmap(): ?array
    {
        $entry = $this->entry('roadmap_config', 'roadmap');

        if ($entry !== null) {
            return [
                'headline' => (string) $entry->get('headline', ''),
                'subheadline' => (string) $entry->get('subheadline', ''),
                'body' => (string) $entry->get('body', ''),
                'integration_milestone_date' => (string) $entry->get('integration_milestone_date', config('catiq.integration_milestone')),
                'integration_milestone_label' => (string) $entry->get('integration_milestone_label', ''),
            ];
        }

        return config('catiq-content.roadmap');
    }

    public function impact(): ?array
    {
        $entry = $this->entry('pages', 'impact');

        if ($entry !== null) {
            return [
                'page_headline' => (string) $entry->get('page_headline', ''),
                'page_subheadline' => (string) $entry->get('page_subheadline', ''),
                'page_body' => (string) $entry->get('page_body', ''),
                'cards' => collect($entry->get('cards', []))->map(fn ($card, $i) => [
                    'index' => $i,
                    'headline' => $card['headline'] ?? '',
                    'body' => $card['body'] ?? '',
                    'metric_label' => $card['metric_label'] ?? '',
                ])->values()->all(),
            ];
        }

        return config('catiq-content.impact');
    }

    private function entry(string $collection, string $slug): ?\Statamic\Contracts\Entries\Entry
    {
        if (! class_exists(Entry::class)) {
            return null;
        }

        try {
            return Entry::query()
                ->where('collection', $collection)
                ->where('slug', $slug)
                ->first();
        } catch (\Throwable) {
            return null;
        }
    }
}
