<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class FilterQuestionsRequest extends FormRequest
{
    /**
     * Allow public read-only question-bank filtering.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for question-bank filters.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'language' => ['nullable', 'string', 'in:en,vi'],
            'family' => ['nullable', 'string', 'max:80'],
            'search' => ['nullable', 'string', 'max:160'],
        ];
    }

    /**
     * Return validated filters for the learning content repository.
     *
     * @return array{language: string|null, family: string|null, search: string|null}
     */
    public function filters(): array
    {
        return [
            'language' => $this->validated('language') ?? 'en',
            'family' => $this->validated('family'),
            'search' => $this->validated('search'),
        ];
    }
}
