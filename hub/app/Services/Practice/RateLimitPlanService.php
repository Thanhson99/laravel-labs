<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class RateLimitPlanService
{
    /**
     * Build a rate-limit plan for sensitive Laravel API endpoints.
     *
     * @param  array{endpoint_name: string, actor_type: string, max_attempts: int, decay_minutes: int, sensitivity: string}  $input
     * @return array{limiter_name: string, middleware: string, retry_after_seconds: int, identity_key: string, response: array<string, string>, steps: array<int, string>, tests: array<int, string>, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $limiterName = 'practice-'.Str::slug($input['endpoint_name']);

        return [
            'limiter_name' => $limiterName,
            'middleware' => "throttle:{$limiterName}",
            'retry_after_seconds' => $input['decay_minutes'] * 60,
            'identity_key' => $this->identityKeyFor($input['actor_type']),
            'response' => [
                'status' => '429 Too Many Requests',
                'header' => 'Retry-After',
                'message' => 'Return a clear limit message without exposing internal limiter keys.',
            ],
            'steps' => [
                'Define the limiter in RouteServiceProvider or bootstrap middleware configuration.',
                'Use a stable actor identity instead of trusting request body fields.',
                'Attach the named throttle middleware to the sensitive route group.',
                'Keep the limit stricter for login, password reset, exports, and webhook endpoints.',
                'Assert success before the limit and HTTP 429 after the limit is exceeded.',
            ],
            'tests' => [
                "Send {$input['max_attempts']} accepted requests for the same actor.",
                'Send one more request and assert HTTP 429.',
                'Assert another actor is not blocked by the first actor key.',
                "Review whether the {$input['sensitivity']} sensitivity level needs logging or alerting.",
            ],
            'commands' => [
                'php artisan route:list --path=rate-limit-plan',
                'php artisan test --filter RateLimitPlanWorkbenchTest',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return the safe limiter key source for the selected actor type.
     */
    private function identityKeyFor(string $actorType): string
    {
        return match ($actorType) {
            'user' => 'Use the authenticated user ID when available.',
            'team' => 'Use the current team or tenant ID plus authenticated user ID.',
            'api-token' => 'Use the token ID or token owner ID, never the raw token value.',
            default => 'Use the request IP address only when no trusted actor exists.',
        };
    }
}
