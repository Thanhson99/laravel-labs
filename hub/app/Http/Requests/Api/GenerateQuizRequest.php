<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class GenerateQuizRequest extends FormRequest
{
    /**
     * Allow public read-only quiz generation.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for quiz generation filters.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'language' => ['nullable', 'string', 'in:en,vi'],
            'family' => ['nullable', 'string', 'max:80'],
            'search' => ['nullable', 'string', 'max:160'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    /**
     * Return validated filters for the quiz service.
     *
     * @return array{language?: string|null, family?: string|null, search?: string|null, limit?: int|null}
     */
    public function filters(): array
    {
        return [
            'language' => $this->validated('language') ?? 'en',
            'family' => $this->validated('family'),
            'search' => $this->validated('search'),
            'limit' => $this->integer('limit') ?: null,
        ];
    }
}
