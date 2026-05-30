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
            'rendering_context' => ['nullable', 'string', 'in:html_text,html_attribute,javascript_data,url,rich_text'],
        ];
    }

    /**
     * Return normalized preview input.
     *
     * @return array{title: string, body: string, rendering_context: string}
     */
    public function content(): array
    {
        $validated = $this->validated();

        return [
            'title' => trim((string) $validated['title']),
            'body' => trim((string) $validated['body']),
            'rendering_context' => (string) ($validated['rendering_context'] ?? 'html_text'),
        ];
    }
}
