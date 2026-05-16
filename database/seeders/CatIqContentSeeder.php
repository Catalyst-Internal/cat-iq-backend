<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;

class CatIqContentSeeder extends Seeder
{
    public function run(): void
    {
        if (! class_exists(Entry::class)) {
            return;
        }

        try {
            Artisan::call('statamic:eloquent-import', ['--force' => true]);
        } catch (\Throwable) {
            // Migrations may already be applied.
        }

        $this->ensureCollection('pages', 'Pages');
        $this->ensureCollection('tiers', 'Tiers');
        $this->ensureCollection('roadmap_config', 'Roadmap config');

        $this->seedPage('home', 'pages', config('catiq-content.home'));
        $this->seedPage('impact', 'pages', config('catiq-content.impact'));
        $this->seedRoadmap();
        $this->seedTiers();
    }

    private function ensureCollection(string $handle, string $title): void
    {
        if (Collection::findByHandle($handle)) {
            return;
        }

        Collection::make($handle)
            ->title($title)
            ->routes('{slug}')
            ->save();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function seedPage(string $slug, string $collection, array $data): void
    {
        if (Entry::query()->where('collection', $collection)->where('slug', $slug)->first()) {
            return;
        }

        $flat = $this->flattenForEntry($data);

        Entry::make()
            ->collection($collection)
            ->slug($slug)
            ->published(true)
            ->data($flat)
            ->save();
    }

    private function seedRoadmap(): void
    {
        if (Entry::query()->where('collection', 'roadmap_config')->where('slug', 'roadmap')->first()) {
            return;
        }

        Entry::make()
            ->collection('roadmap_config')
            ->slug('roadmap')
            ->published(true)
            ->data(config('catiq-content.roadmap'))
            ->save();
    }

    private function seedTiers(): void
    {
        foreach (config('catiq-content.tiers', []) as $slug => $data) {
            if (Entry::query()->where('collection', 'tiers')->where('slug', $slug)->first()) {
                continue;
            }

            Entry::make()
                ->collection('tiers')
                ->slug($slug)
                ->published(true)
                ->data($data)
                ->save();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function flattenForEntry(array $data): array
    {
        $flat = [];

        foreach ($data as $key => $value) {
            if (is_array($value) && array_is_list($value)) {
                $flat[$key] = $value;

                continue;
            }

            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $flat["{$key}_{$subKey}"] = $subValue;
                }

                continue;
            }

            $flat[$key] = $value;
        }

        return $flat;
    }
}
