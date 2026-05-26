<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class FileStoragePlanService
{
    /**
     * Build a file storage plan for upload and media practice.
     *
     * @param  array{purpose: string, disk: string, visibility: string, max_mb: int}  $input
     * @return array{disk: string, visibility: string, path_pattern: string, validation_rules: array<int, string>, lifecycle: array<int, string>, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $purpose = Str::slug($input['purpose']);

        return [
            'disk' => $input['disk'],
            'visibility' => $input['visibility'],
            'path_pattern' => "{$purpose}/{user_id}/{uuid}.{extension}",
            'validation_rules' => [
                'required',
                'file',
                'max:'.($input['max_mb'] * 1024),
                'mimes:jpg,jpeg,png,pdf',
            ],
            'lifecycle' => [
                'Validate file type and size before storage.',
                'Store under a scoped path that includes owner or tenant identity.',
                'Persist only the path, disk, mime type, and size needed by the app.',
                'Delete or replace the old file when the owning record changes.',
                'Use signed URLs for private downloads when the disk supports them.',
            ],
            'commands' => [
                'php artisan storage:link',
                'php artisan test --filter FileStoragePlanWorkbenchTest',
                'vendor\\bin\\pint --test',
            ],
        ];
    }
}
