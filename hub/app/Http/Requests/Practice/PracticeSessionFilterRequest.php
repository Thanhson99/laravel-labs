<?php

declare(strict_types=1);

namespace App\Http\Requests\Practice;

use Illuminate\Foundation\Http\FormRequest;

final class PracticeSessionFilterRequest extends FormRequest
{
    /**
     * Allow public session plan filtering.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate practice session filters.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'track' => ['nullable', 'string', 'max:80'],
        ];
    }

    /**
     * Return the selected track, if provided.
     */
    public function selectedTrack(): ?string
    {
        return $this->string('track')->toString() ?: null;
    }
}
