<?php

declare(strict_types=1);

namespace App\Http\Requests\Practice;

use Illuminate\Foundation\Http\FormRequest;

final class PracticeContentFilterRequest extends FormRequest
{
    /**
     * Allow public practice pages and APIs to resolve content-backed practice artifacts.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate filters that identify a source record or related technology.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'family' => ['nullable', 'string', 'max:80'],
            'language' => ['nullable', 'string', 'max:10'],
            'search' => ['nullable', 'string', 'max:120'],
            'technology' => ['nullable', 'string', 'max:120'],
            'source_key' => ['nullable', 'string', 'max:180'],
            'record_id' => ['nullable', 'string', 'max:180'],
        ];
    }

    /**
     * Build normalized filters for source-record practice flows.
     *
     * @return array{
     *     family: string|null,
     *     language: string|null,
     *     search: string|null,
     *     technology: string|null,
     *     source_key: string|null,
     *     record_id: string|null
     * }
     */
    public function contentFilters(): array
    {
        $sourceKey = $this->string('source_key')->toString() ?: null;
        $recordId = $this->string('record_id')->toString() ?: null;
        $usesExactRecordLookup = $sourceKey !== null || $recordId !== null;

        return [
            'family' => $this->string('family')->toString() ?: ($usesExactRecordLookup ? null : 'laravel'),
            'language' => $this->string('language')->toString() ?: ($usesExactRecordLookup ? null : 'en'),
            'search' => $this->string('search')->toString() ?: ($usesExactRecordLookup ? null : 'api'),
            'technology' => $this->string('technology')->toString() ?: null,
            'source_key' => $sourceKey,
            'record_id' => $recordId,
        ];
    }
}
