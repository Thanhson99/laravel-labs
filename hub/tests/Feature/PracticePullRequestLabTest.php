<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticePullRequestLabTest extends TestCase
{
    /**
     * The PR lab page renders branch, commit, and PR draft data.
     */
    public function test_pull_request_lab_page_renders_pr_artifacts(): void
    {
        $response = $this->get('/practice/pull-request-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('PR lab packages practice work like a real Laravel change.')
            ->assertSee('practice/api-validation/what-is-an-api-in-a-laravel-context')
            ->assertSee('practice: implement what-is-an-api-in-a-laravel-context')
            ->assertSee('PR Progress Payload');
    }

    /**
     * The PR lab API returns branch, commit, pull request, and progress payload.
     */
    public function test_pull_request_lab_api_returns_pr_artifacts(): void
    {
        $response = $this->getJson('/api/practice/pull-request-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.branch', 'practice/api-validation/what-is-an-api-in-a-laravel-context')
            ->assertJsonPath('data.commit_message', 'practice: implement what-is-an-api-in-a-laravel-context lab')
            ->assertJsonPath('data.pull_request.changed_files.0', 'routes/api.php')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source',
                    'technology',
                    'record',
                    'branch',
                    'commit_message',
                    'pull_request' => [
                        'title',
                        'summary',
                        'changed_files',
                        'verification',
                        'review_checklist',
                    ],
                    'quality_gate_payload',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * The PR lab API returns 404 for unknown records.
     */
    public function test_pull_request_lab_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/pull-request-lab?record_id=missing-record');

        $response->assertNotFound();
    }

    /**
     * JavaScript closure PR labs package interview evidence instead of generic Laravel slices.
     */
    public function test_javascript_closure_pull_request_lab_uses_scope_summary(): void
    {
        $response = $this->getJson('/api/practice/pull-request-lab?record_id=laravel-frontend-en-json-item-8&technology=javascript-closures');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.branch', 'practice/javascript-closures/what-is-a-closure-in-javascript-and-why-is-it-importan')
            ->assertJsonPath('data.pull_request.summary.0', 'Implements a JavaScript closure interview artifact for `What is a closure in JavaScript, and why is it important?`.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Create closure practice branch')
            ->assertJsonPath('data.progress_payload.items.3.label', 'Paste lexical-scope and stale-closure verification evidence');

        $this->assertStringContainsString('closure evidence fixed', $response->json('data.pull_request.review_checklist.0'));
        $this->assertStringContainsString('lexical-scope proof', $response->json('data.pull_request.summary.2'));
    }

    /**
     * IDOR PR labs package object-authorization evidence.
     */
    public function test_idor_pull_request_lab_uses_object_authorization_summary(): void
    {
        $response = $this->getJson('/api/practice/pull-request-lab?record_id=laravel-auth-security-en-json-item-113&technology=idor-access-control');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.branch', 'practice/idor-access-control/understand-idor-through-a-real-api-example')
            ->assertJsonPath('data.pull_request.summary.0', 'Implements an IDOR object-authorization artifact for `113. Understand IDOR through a real API example`.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Create IDOR practice branch')
            ->assertJsonPath('data.progress_payload.items.3.label', 'Paste scoped lookup, policy, and ID-swap denial verification evidence');

        $this->assertStringContainsString('object-authorization evidence fixed', $response->json('data.pull_request.review_checklist.0'));
        $this->assertStringContainsString('ID-swap denial tests', $response->json('data.pull_request.summary.2'));
    }
}
