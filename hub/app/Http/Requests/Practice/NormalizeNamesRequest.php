<?php

declare(strict_types=1);

namespace App\Http\Requests\Practice;

use Illuminate\Foundation\Http\FormRequest;

final class NormalizeNamesRequest extends FormRequest
{
    /**
     * Allow public practice requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for name normalization practice.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'names' => ['required', 'array', 'min:1', 'max:50'],
            'names.*' => ['nullable', 'string', 'max:120'],
        ];
    }

    /**
     * Return raw names from the validated request.
     *
     * @return array<int, string>
     */
    public function names(): array
    {
        return array_values(array_map('strval', $this->validated('names')));
    }
}
