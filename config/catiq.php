<?php

return [

    'integration_milestone' => env('CATIQ_INTEGRATION_MILESTONE', '2026-07-01'),

    /**
     * Allowed email domains for login/register. Empty = no restriction.
     *
     * @return list<string>
     */
    'allowed_email_domains' => array_values(array_filter(array_map(
        static fn (string $domain): string => strtolower(trim($domain)),
        explode(',', (string) env('ALLOWED_EMAIL_DOMAINS', ''))
    ))),

  /**
     * Product tier slug => GitHub repository name (within GITHUB_ORG).
     *
     * @var array<string, string>
     */
    'tiers' => [
        'data-warehouse' => 'cat-iq-data',
        'claude-os' => 'claude-os',
        'datalyst' => 'datalyst',
    ],

    'tier_labels' => [
        'data-warehouse' => 'DW',
        'claude-os' => 'CO',
        'datalyst' => 'DL',
    ],

];
