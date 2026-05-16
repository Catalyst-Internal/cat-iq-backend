<?php

/**
 * Fallback evergreen content when Statamic entries are not yet seeded.
 * Shape matches shared/protocols/api-contracts.md.
 * Home + tier copy aligned with apps/frontend/lib/stubs/* (final designs).
 */
return [
    'home' => [
        'hero' => [
            'headline' => "Three connected layers.\nSame team, exponentially more output.",
            'subheadline' => 'One reliable source of truth. An AI-native interface for the team. A client-facing dashboard built on top. Catalyst iQ is the platform under everything Catalyst Marketing ships next.',
            'cta_label' => 'See the system →',
            'cta_url' => '#system',
            'secondary_label' => 'View the roadmap',
            'secondary_url' => '/roadmap',
        ],
        'why' => [
            'headline' => 'Operational friction is replaced by sustainable acceleration.',
            'body' => '<p>Every Catalyst team member gets an AI agent partner. Manual workflows automate. Strategy, creative, and client relationships — the work only humans can do — get the time back.</p><p>A marketing agency that executes at AI scale doesn\'t compete on price. It competes on a fundamentally different plane.</p>',
        ],
        'layers' => [
            'headline' => 'One system. Three layers.',
            'subheadline' => 'Each tier depends on the one before it. Dependency, not coincidence. Click any layer to see the team, current focus, and what ships next.',
        ],
        'impact' => [
            'headline' => 'Same team. Exponentially more output.',
        ],
    ],

    'tiers' => [
        'data-warehouse' => [
            'tier_number' => 1,
            'title' => 'Data Warehouse',
            'subtitle' => 'One reliable source of truth for everything Catalyst does.',
            'description_long' => '<p>Data Warehouse collects and organizes the business data that powers every other layer — a unified, queryable layer aggregating campaign, leasing, creative, and channel data.</p>',
            'outcome' => '<p>Once live, every dashboard, every Claude-driven analysis, every Datalyst surface reads from one place. No more spreadsheets reconciled by hand. No more \'which number is right?\'</p>',
            'anatomy_content' => null,
            'team' => [],
            'github_repo' => 'cat-iq-data',
            'prev_tier_slug' => null,
            'next_tier_slug' => 'claude-os',
            'seo_title' => null,
            'seo_description' => null,
        ],
        'claude-os' => [
            'tier_number' => 2,
            'title' => 'Claude OS',
            'subtitle' => 'An AI-native interface for the Catalyst team.',
            'description_long' => '<p>Claude OS is the AI layer that automates internal workflows and surfaces insight for SEO, paid media, creative, and scheduling. Every Catalyst team member has an AI agent partner. Operational friction is replaced by Sustainable Acceleration.</p>',
            'outcome' => '<p>Parallel workflows across every client. Reports in minutes, not days. Real-time intelligence on demand.</p>',
            'anatomy_content' => null,
            'team' => [],
            'github_repo' => 'claude-os',
            'prev_tier_slug' => 'data-warehouse',
            'next_tier_slug' => 'datalyst',
            'seo_title' => null,
            'seo_description' => null,
        ],
        'datalyst' => [
            'tier_number' => 3,
            'title' => 'Datalyst',
            'subtitle' => 'Real-time visibility for subscribers, built on the warehouse.',
            'description_long' => '<p>The client-facing dashboard — and adjacent subscriber products (sites, social content) — powered by the warehouse underneath. Datalyst is flexible and configurable for global consumption.</p>',
            'outcome' => '<p>What clients get: direct visibility, transparency, faster decisions. What Catalyst gets: a productized layer to monetize.</p>',
            'anatomy_content' => null,
            'team' => [],
            'github_repo' => 'datalyst',
            'prev_tier_slug' => 'claude-os',
            'next_tier_slug' => null,
            'seo_title' => null,
            'seo_description' => null,
        ],
    ],

    'roadmap' => [
        'headline' => 'Roadmap',
        'subheadline' => 'Three tiers converging on integration',
        'body' => '<p>Milestones tracked from GitHub across all product tiers.</p>',
        'integration_milestone_date' => '2026-07-01',
        'integration_milestone_label' => 'Full stack integration',
    ],

    'impact' => [
        'page_headline' => 'Impact',
        'page_subheadline' => 'Outcomes we measure',
        'page_body' => '<p>Metrics reserved for Stage 5.</p>',
        'cards' => [],
    ],
];
