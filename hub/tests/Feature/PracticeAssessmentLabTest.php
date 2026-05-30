<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeAssessmentLabTest extends TestCase
{
    /**
     * The assessment lab page renders a scoring rubric.
     */
    public function test_assessment_lab_page_renders_rubric(): void
    {
        $response = $this->get('/practice/assessment-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('Assessment lab scores Laravel practice work with a clear rubric.')
            ->assertSee('Rubric: 100 points')
            ->assertSee('Laravel layer boundaries')
            ->assertSee('Assessment Progress Payload');
    }

    /**
     * The assessment lab API returns rubric, evidence, and progress payload.
     */
    public function test_assessment_lab_api_returns_rubric_and_evidence(): void
    {
        $response = $this->getJson('/api/practice/assessment-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.score_total', 100)
            ->assertJsonPath('data.rubric.0.label', 'Content traceability')
            ->assertJsonPath('data.evidence.changed_files.0', 'routes/api.php')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source',
                    'technology',
                    'record',
                    'branch',
                    'commit_message',
                    'rubric' => [
                        '*' => [
                            'label',
                            'points',
                            'evidence',
                            'self_check',
                        ],
                    ],
                    'score_total',
                    'evidence',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * The assessment lab API returns 404 for unknown records.
     */
    public function test_assessment_lab_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/assessment-lab?record_id=missing-record');

        $response->assertNotFound();
    }

    /**
     * JavaScript closure assessments score lexical scope, behavior tests, and interview readiness.
     */
    public function test_javascript_closure_assessment_lab_uses_scope_rubric(): void
    {
        $response = $this->getJson('/api/practice/assessment-lab?record_id=laravel-frontend-en-json-item-8&technology=javascript-closures');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.score_total', 100)
            ->assertJsonPath('data.rubric.1.label', 'Lexical scope model')
            ->assertJsonPath('data.rubric.2.label', 'Closure behavior tests')
            ->assertJsonPath('data.rubric.4.label', 'Interview readiness');

        $this->assertStringContainsString('captured binding', $response->json('data.rubric.1.evidence'));
        $this->assertStringContainsString('stale-closure output', $response->json('data.rubric.2.evidence'));
    }
}
