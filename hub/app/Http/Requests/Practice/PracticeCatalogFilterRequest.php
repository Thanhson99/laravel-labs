<?php

declare(strict_types=1);

namespace App\Http\Requests\Practice;

use Illuminate\Foundation\Http\FormRequest;

final class PracticeCatalogFilterRequest extends FormRequest
{
    /**
     * Allow public catalog filtering.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate native practice catalog filters.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'track' => ['nullable', 'string', 'max:80'],
            'search' => ['nullable', 'string', 'max:120'],
        ];
    }

    /**
     * Build normalized catalog filters.
     *
     * @return array{track: string|null, search: string|null}
     */
    public function catalogFilters(): array
    {
        return [
            'track' => $this->string('track')->toString() ?: null,
            'search' => $this->string('search')->toString() ?: null,
        ];
    }
}
