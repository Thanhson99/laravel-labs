<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationPullRequestPlanService
{
    /**
     * Create pull request plans from configuration remediation tasks.
     */
    public function __construct(
        private readonly ConfigurationRemediationPlanService $remediationPlan,
    ) {}

    /**
     * Build PR-ready artifacts for configuration remediation work.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $plan = $this->remediationPlan->build();
        $changedFiles = $this->changedFiles($plan['tasks']);

        return [
            'title' => 'Configuration Pull Request Plan',
            'summary' => 'Package configuration remediation into a reviewable pull request with branch, commit, changed files, verification evidence, and reviewer focus.',
            'branch' => 'practice/configuration-risk-remediation',
            'commit_message' => 'practice: add configuration risk remediation plan',
            'changed_files' => $changedFiles,
            'pr_summary' => [
                'Remediates '.$plan['task_count'].' configuration risks across app runtime, auth contract, quality gate, and release evidence.',
                'Adds Security Misconfiguration release-blocker evidence for debug, secrets, CORS, headers, cookies, proxies, and storage exposure.',
                'Keeps configuration learning artifacts read-only and testable.',
                'Preserves shared quality-gate status semantics for downstream practice pages.',
            ],
            'review_checklist' => [
                'Target files match the risks being remediated.',
                'High-severity risks include focused verification commands.',
                'Security Misconfiguration controls include owner, rollback, release blocker, and fail-closed smoke evidence.',
                'No config values are hardcoded into controllers or views.',
                'Docs mention new durable routes and practice artifacts.',
            ],
            'verification' => $this->verification($plan),
            'evidence' => [
                'Risk register lists '.$plan['risk_count'].' risks with owner routes.',
                'Security Misconfiguration release blocker maps unsafe production signals to readiness and deployment evidence.',
                'Remediation plan maps each risk to target files and done signals.',
                'Route documentation test confirms new route comments remain learner-facing.',
            ],
            'commands' => [
                ...$plan['commands'],
                'php artisan test --filter ConfigurationPullRequestPlanTest',
            ],
            'status' => $plan['status'],
        ];
    }

    /**
     * Build a unique changed-file list from remediation tasks.
     *
     * @param  array<int, array<string, mixed>>  $tasks
     * @return array<int, string>
     */
    private function changedFiles(array $tasks): array
    {
        return collect($tasks)
            ->flatMap(fn (array $task): array => $task['target_files'])
            ->push('hub/app/Services/Practice/ConfigurationPullRequestPlanService.php')
            ->push('hub/tests/Feature/ConfigurationPullRequestPlanTest.php')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Build verification commands from remediation tasks.
     *
     * @param  array<string, mixed>  $plan
     * @return array<int, string>
     */
    private function verification(array $plan): array
    {
        return collect($plan['tasks'])
            ->flatMap(fn (array $task): array => $task['verification'])
            ->push('php artisan test --filter RouteFileDocumentationTest')
            ->push('vendor\\bin\\pint --test')
            ->unique()
            ->values()
            ->all();
    }
}
