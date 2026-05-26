<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class SummarizePracticeProgressRequest extends FormRequest
{
    /**
     * Allow public practice progress requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for checklist progress.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.label' => ['required', 'string', 'min:3', 'max:120'],
            'items.*.done' => ['required', 'boolean'],
        ];
    }

    /**
     * Return normalized checklist items.
     *
     * @return array<int, array{label: string, done: bool}>
     */
    public function items(): array
    {
        return array_values(array_map(
            fn (array $item): array => [
                'label' => trim((string) $item['label']),
                'done' => (bool) $item['done'],
            ],
            $this->validated('items'),
        ));
    }
}
