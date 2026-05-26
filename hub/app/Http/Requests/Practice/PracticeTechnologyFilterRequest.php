<?php

declare(strict_types=1);

namespace App\Http\Requests\Practice;

use Illuminate\Foundation\Http\FormRequest;

final class PracticeTechnologyFilterRequest extends FormRequest
{
    /**
     * Allow public practice pages and APIs to filter technology-level practice output.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate shared technology filter query parameters.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'technology' => ['nullable', 'string', 'max:120'],
            'family' => ['nullable', 'string', 'max:80'],
            'language' => ['nullable', 'string', 'max:10'],
            'search' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Build filters for pages that require a concrete technology selection.
     *
     * @return array{technology: string, family: string, language: string, search: string|null, limit: int}
     */
    public function requiredTechnologyFilters(int $defaultLimit, ?string $defaultSearch = 'api'): array
    {
        return [
            'technology' => $this->string('technology')->toString() ?: 'api-validation',
            'family' => $this->string('family')->toString() ?: 'laravel',
            'language' => $this->string('language')->toString() ?: 'en',
            'search' => $this->string('search')->toString() ?: $defaultSearch,
            'limit' => $this->integer('limit') ?: $defaultLimit,
        ];
    }

    /**
     * Build filters for pages where technology can be absent.
     *
     * @return array{family: string|null, language: string|null, search: string|null, technology: string|null, limit: int}
     */
    public function optionalTechnologyFilters(
        int $defaultLimit,
        ?string $defaultFamily = 'laravel',
        ?string $defaultLanguage = 'en',
        ?string $defaultSearch = 'api'
    ): array {
        return [
            'family' => $this->string('family')->toString() ?: $defaultFamily,
            'language' => $this->string('language')->toString() ?: $defaultLanguage,
            'search' => $this->string('search')->toString() ?: $defaultSearch,
            'technology' => $this->string('technology')->toString() ?: null,
            'limit' => $this->integer('limit') ?: $defaultLimit,
        ];
    }
}
