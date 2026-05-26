<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class CacheStrategyPlanService
{
    /**
     * Build a cache strategy plan for read-heavy Laravel flows.
     *
     * @param  array{resource_name: string, scope: string, ttl_minutes: int, invalidation_trigger: string}  $input
     * @return array{cache_key: string, ttl_seconds: int, strategy: array<string, string>, steps: array<int, string>, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $resource = Str::slug($input['resource_name']);
        $scope = Str::slug($input['scope']);
        $ttlSeconds = $input['ttl_minutes'] * 60;

        return [
            'cache_key' => "practice:{$resource}:{$scope}",
            'ttl_seconds' => $ttlSeconds,
            'strategy' => [
                'read_pattern' => 'Use Cache::remember for repeated read-heavy data.',
                'invalidation_trigger' => trim($input['invalidation_trigger']),
                'fallback' => 'Always keep the source query or service path correct when the cache is empty.',
                'risk' => 'Stale cache is a product bug when the invalidation owner is unclear.',
            ],
            'steps' => [
                'Name the cache key with resource and scope.',
                'Choose a TTL that matches how stale the data may safely be.',
                'Document the exact event or write path that invalidates the key.',
                'Test both cache miss and cache hit behavior.',
                'Avoid caching user-specific data under a shared key.',
            ],
            'commands' => [
                'php artisan cache:clear',
                'php artisan optimize:clear',
                'php artisan test --filter CacheStrategyPlanWorkbenchTest',
            ],
        ];
    }
}
