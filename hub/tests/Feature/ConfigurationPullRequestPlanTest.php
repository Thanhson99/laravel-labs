<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationPullRequestPlanTest extends TestCase
{
    /**
     * The configuration pull request plan page renders PR artifacts.
     */
    public function test_configuration_pull_request_plan_page_renders_pr_artifacts(): void
    {
        $response = $this->get('/practice/configuration-pull-request-plan');

        $response
            ->assertOk()
            ->assertSee('Configuration Pull Request Plan')
            ->assertSee('practice/configuration-risk-remediation')
            ->assertSee('Changed Files')
            ->assertSee('Review Checklist')
            ->assertSee('Evidence')
            ->assertSee('Security Misconfiguration release blocker maps unsafe production signals')
            ->assertSee('Open PR API');
    }

    /**
     * The configuration pull request plan API returns branch, files, and verification.
     */
    public function test_configuration_pull_request_plan_api_returns_pr_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-pull-request-plan');

        $response
            ->assertOk()
            ->assertJsonPath('data.branch', 'practice/configuration-risk-remediation')
            ->assertJsonPath('data.commit_message', 'practice: add configuration risk remediation plan')
            ->assertJsonPath('data.status.quality', 'ready')
            ->assertJsonPath('data.pr_summary.1', 'Adds Security Misconfiguration release-blocker evidence for debug, secrets, CORS, headers, cookies, proxies, and storage exposure.')
            ->assertJsonPath('data.review_checklist.2', 'Security Misconfiguration controls include owner, rollback, release blocker, and fail-closed smoke evidence.')
            ->assertJsonPath('data.evidence.0', 'Risk register lists 5 risks with owner routes.')
            ->assertJsonPath('data.evidence.1', 'Security Misconfiguration release blocker maps unsafe production signals to readiness and deployment evidence.')
            ->assertJsonFragment(['hub/config/auth.php'])
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'branch',
                    'commit_message',
                    'changed_files',
                    'pr_summary',
                    'review_checklist',
                    'verification',
                    'evidence',
                    'commands',
                    'status',
                ],
            ]);
    }
}
