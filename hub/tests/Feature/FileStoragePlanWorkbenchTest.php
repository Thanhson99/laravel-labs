<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class FileStoragePlanWorkbenchTest extends TestCase
{
    /**
     * The file storage workbench renders the upload lifecycle planning loop.
     */
    public function test_file_storage_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/file-storage-plan');

        $response
            ->assertOk()
            ->assertSee('File Storage Plan Workbench')
            ->assertSee('POST /api/practice/file-storage-plan')
            ->assertSee('FileStoragePlanService')
            ->assertSee('Plan storage');
    }

    /**
     * The file storage API returns validation, path, lifecycle, and command details.
     */
    public function test_file_storage_plan_api_returns_plan(): void
    {
        $response = $this->postJson('/api/practice/file-storage-plan', [
            'purpose' => 'Profile avatar',
            'disk' => 'public',
            'visibility' => 'public',
            'max_mb' => 5,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.disk', 'public')
            ->assertJsonPath('data.visibility', 'public')
            ->assertJsonPath('data.path_pattern', 'profile-avatar/{user_id}/{uuid}.{extension}')
            ->assertJsonPath('data.validation_rules.2', 'max:5120')
            ->assertJsonPath('data.commands.0', 'php artisan storage:link');
    }

    /**
     * Invalid file storage payloads return validation errors.
     */
    public function test_file_storage_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/file-storage-plan', [
            'purpose' => 'x',
            'disk' => 'ftp',
            'visibility' => 'shared',
            'max_mb' => 0,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['purpose', 'disk', 'visibility', 'max_mb']);
    }
}
