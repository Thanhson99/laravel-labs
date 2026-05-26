<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationAssessmentService
{
    /**
     * Score configuration remediation pull request readiness.
     */
    public function __construct(
        private readonly ConfigurationPullRequestPlanService $pullRequestPlan,
    ) {}

    /**
     * Build a scored assessment for configuration remediation work.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $plan = $this->pullRequestPlan->build();
        $rubric = $this->rubric($plan);
        $score = collect($rubric)->sum('points');
        $result = $score >= 85 && $plan['status']['quality'] === 'ready' ? 'ready' : 'needs-work';

        return [
            'title' => 'Configuration Assessment',
            'summary' => 'Score configuration remediation PR readiness before the learner reuses the work in release evidence, portfolio notes, or interview defense.',
            'score' => $score,
            'result' => $result,
            'result_label' => $result === 'ready' ? 'ready with '.$score.' points' : 'needs work with '.$score.' points',
            'rubric' => $rubric,
            'readiness_signals' => [
                'Branch '.$plan['branch'].' keeps remediation work focused.',
                'Commit message is reviewable: '.$plan['commit_message'],
                'Changed files include configuration, service, route, view, and test evidence.',
                'Verification includes route documentation, targeted feature tests, and Pint.',
            ],
            'improvement_tasks' => [
                'Paste the assessment score into the pull request description before review.',
                'Attach one screenshot or copied API payload from the configuration assessment endpoint.',
                'Re-run the commands after any config, route, service, or view change.',
                'Move repeated failures back to the remediation plan instead of hiding them in release notes.',
            ],
            'evidence' => $plan['evidence'],
            'commands' => [
                ...$plan['commands'],
                'php artisan test --filter ConfigurationAssessmentTest',
            ],
            'status' => $plan['status'],
        ];
    }

    /**
     * Build the rubric from measurable pull request plan signals.
     *
     * @param  array<string, mixed>  $plan
     * @return array<int, array<string, mixed>>
     */
    private function rubric(array $plan): array
    {
        return [
            [
                'criterion' => 'Scope control',
                'points' => filled($plan['branch']) && count($plan['pr_summary']) >= 3 ? 20 : 10,
                'max_points' => 20,
                'evidence' => 'Focused branch and PR summary explain the configuration remediation boundary.',
            ],
            [
                'criterion' => 'Configuration coverage',
                'points' => $this->containsAny($plan['changed_files'], ['hub/config/app.php', 'hub/config/auth.php']) ? 25 : 15,
                'max_points' => 25,
                'evidence' => 'Changed files include app/auth configuration contracts and adjacent practice artifacts.',
            ],
            [
                'criterion' => 'Quality gate contract',
                'points' => $plan['status']['quality'] === 'ready' ? 25 : 10,
                'max_points' => 25,
                'evidence' => 'Shared practice quality status is reused instead of inventing a new pass/fail shape.',
            ],
            [
                'criterion' => 'Verification depth',
                'points' => $this->containsAny($plan['verification'], ['RouteFileDocumentationTest'])
                    && in_array('vendor\\bin\\pint --test', $plan['verification'], true) ? 20 : 10,
                'max_points' => 20,
                'evidence' => 'Verification covers route comments, focused tests, and code style.',
            ],
            [
                'criterion' => 'Evidence reuse',
                'points' => count($plan['evidence']) >= 3 ? 10 : 5,
                'max_points' => 10,
                'evidence' => 'The plan has enough evidence for release notes, portfolio notes, and interview defense.',
            ],
        ];
    }

    /**
     * Check whether a list contains at least one expected value.
     *
     * @param  array<int, string>  $items
     * @param  array<int, string>  $needles
     */
    private function containsAny(array $items, array $needles): bool
    {
        return collect($needles)->contains(
            fn (string $needle): bool => collect($items)->contains(
                fn (string $item): bool => str_contains($item, $needle),
            ),
        );
    }
}
