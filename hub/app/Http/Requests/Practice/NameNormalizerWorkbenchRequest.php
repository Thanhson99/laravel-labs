<?php

declare(strict_types=1);

namespace App\Http\Requests\Practice;

use Illuminate\Foundation\Http\FormRequest;

final class NameNormalizerWorkbenchRequest extends FormRequest
{
    /**
     * Allow public workbench rendering.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate optional newline-separated workbench input.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'names' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Return raw newline-separated names for the workbench.
     */
    public function rawNames(): string
    {
        return $this->string('names')->toString() ?: "  jane   doe\nLARAVEL labs\n\napi practice";
    }
}
