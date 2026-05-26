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
