<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanFileStorageRequest extends FormRequest
{
    /**
     * Allow public file storage planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for file storage planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'purpose' => ['required', 'string', 'min:3', 'max:80'],
            'disk' => ['required', 'string', Rule::in(['local', 'public', 's3'])],
            'visibility' => ['required', 'string', Rule::in(['private', 'public'])],
            'max_mb' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Return normalized file storage planning input.
     *
     * @return array{purpose: string, disk: string, visibility: string, max_mb: int}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'purpose' => trim((string) $validated['purpose']),
            'disk' => (string) $validated['disk'],
            'visibility' => (string) $validated['visibility'],
            'max_mb' => (int) $validated['max_mb'],
        ];
    }
}
