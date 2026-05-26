<?php

declare(strict_types=1);

namespace App\Http\Requests\Practice;

use Illuminate\Foundation\Http\FormRequest;

final class PracticeSourceFilterRequest extends FormRequest
{
    /**
     * Allow public source and syllabus pages to filter integrated content.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate source-list filters shared by syllabus, packs, and matrix pages.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'family' => ['nullable', 'string', 'max:80'],
            'language' => ['nullable', 'string', 'max:10'],
            'search' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Build normalized source filters.
     *
     * @return array<string, int|string|null>
     */
    public function sourceFilters(
        ?string $defaultFamily = null,
        ?string $defaultLanguage = null,
        ?string $defaultSearch = null,
        ?int $defaultLimit = null
    ): array {
        $filters = [
            'family' => $this->string('family')->toString() ?: $defaultFamily,
            'language' => $this->string('language')->toString() ?: $defaultLanguage,
            'search' => $this->string('search')->toString() ?: $defaultSearch,
        ];

        if ($defaultLimit !== null) {
            $filters['limit'] = $this->integer('limit') ?: $defaultLimit;
        }

        return $filters;
    }
}
