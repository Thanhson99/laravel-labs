<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeRemediationLabTest extends TestCase
{
    /**
     * The remediation lab page renders concrete fix tasks.
     */
    public function test_remediation_lab_page_renders_fix_tasks(): void
    {
        $response = $this->get('/practice/remediation-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('Remediation lab turns review findings into concrete code fixes.')
            ->assertSee('Fix 1')
            ->assertSee('Move input rules into the Form Request')
            ->assertSee('Remediation Progress Payload');
    }

    /**
     * The remediation lab API returns tasks and progress payload.
     */
    public function test_remediation_lab_api_returns_tasks_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/remediation-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.tasks.1.label', 'Route contract')
            ->assertJsonPath('data.tasks.1.verification', 'php artisan route:list --path=api/practice')
            ->assertJsonPath('data.tasks.2.file', 'app/Http/Requests/Api/StoreWhatIsAnApiInALaravelContextRequest.php')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source',
                    'technology',
                    'record',
                    'route',
                    'tasks' => [
                        '*' => [
                            'position',
                            'label',
                            'file',
                            'problem_to_check',
                            'fix_action',
                            'verification',
                        ],
                    ],
                    'quality_gate_payload',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * The remediation lab API returns 404 for unknown records.
     */
    public function test_remediation_lab_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/remediation-lab?record_id=missing-record');

        $response->assertNotFound();
    }
}
