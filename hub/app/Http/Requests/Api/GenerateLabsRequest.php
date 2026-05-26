<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class GenerateLabsRequest extends FormRequest
{
    /**
     * Allow public read-only lab generation.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for lab generation filters.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'language' => ['nullable', 'string', 'in:en,vi'],
            'family' => ['nullable', 'string', 'max:80'],
            'track' => ['nullable', 'string', Rule::in([
                'php',
                'laravel',
                'api',
                'testing',
                'docker-devops',
                'ai-workflow',
                'interview',
            ])],
            'search' => ['nullable', 'string', 'max:160'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ];
    }

    /**
     * Return validated filters for the lab service.
     *
     * @return array{language?: string|null, family?: string|null, track?: string|null, search?: string|null, limit?: int|null}
     */
    public function filters(): array
    {
        return [
            'language' => $this->validated('language') ?? 'en',
            'family' => $this->validated('family'),
            'track' => $this->validated('track'),
            'search' => $this->validated('search'),
            'limit' => $this->integer('limit') ?: null,
        ];
    }
}
