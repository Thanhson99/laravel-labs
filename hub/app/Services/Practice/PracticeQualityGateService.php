<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeQualityGateService
{
    /**
     * Evaluate practice verification results.
     *
     * @param  array{tests: int, assertions: int, failures: int, pint: bool}  $results
     * @return array{status: string, passed: bool, checks: array<string, bool>, next_action: string}
     */
    public function evaluate(array $results): array
    {
        $checks = [
            'tests_exist' => $results['tests'] > 0,
            'assertions_exist' => $results['assertions'] > 0,
            'tests_pass' => $results['failures'] === 0,
            'style_passes' => $results['pint'],
        ];

        $passed = ! in_array(false, $checks, true);

        return [
            'status' => $passed ? 'ready' : 'needs-work',
            'passed' => $passed,
            'checks' => $checks,
            'next_action' => $passed
                ? 'Refactor only after the behavior is protected by tests.'
                : 'Fix the failing check, then rerun php artisan test and vendor\\bin\\pint --test.',
        ];
    }
}
