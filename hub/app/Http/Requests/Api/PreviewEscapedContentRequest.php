<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class PreviewEscapedContentRequest extends FormRequest
{
    /**
     * Allow public practice API requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for escaped content preview input.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    /**
     * Return normalized preview input.
     *
     * @return array{title: string, body: string}
     */
    public function content(): array
    {
        $validated = $this->validated();

        return [
            'title' => trim((string) $validated['title']),
            'body' => trim((string) $validated['body']),
        ];
    }
}
