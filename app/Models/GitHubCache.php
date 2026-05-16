<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GitHubCache extends Model
{
    protected $table = 'github_cache';

    protected $fillable = [
        'cache_key',
        'payload',
        'ttl_seconds',
        'fetched_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'fetched_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
