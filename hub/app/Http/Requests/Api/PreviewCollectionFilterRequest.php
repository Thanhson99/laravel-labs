<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PreviewCollectionFilterRequest extends FormRequest
{
    /**
     * Allow public practice API requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for list-filter preview input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:80'],
            'status' => ['sometimes', 'string', Rule::in(['all', 'open', 'in-review', 'done'])],
            'page' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:10'],
        ];
    }

    /**
     * Return normalized filter input for the service.
     *
     * @return array{search: string, status: string, page: int, per_page: int}
     */
    public function filters(): array
    {
        $validated = $this->validated();

        return [
            'search' => trim((string) ($validated['search'] ?? '')),
            'status' => (string) ($validated['status'] ?? 'all'),
            'page' => (int) ($validated['page'] ?? 1),
            'per_page' => (int) ($validated['per_page'] ?? 3),
        ];
    }
}
