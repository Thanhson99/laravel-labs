<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class TraceHttpRequestFlowRequest extends FormRequest
{
    /**
     * Allow public practice API requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for the request-flow tracer.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'method' => ['required', 'string', Rule::in(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])],
            'path' => ['required', 'string', 'min:1', 'max:120', 'regex:/^[A-Za-z0-9_\/{}?=&.-]+$/'],
            'accept' => ['sometimes', 'string', Rule::in(['text/html', 'application/json'])],
        ];
    }

    /**
     * Return normalized trace input for the service.
     *
     * @return array{method: string, path: string, accept: string}
     */
    public function traceData(): array
    {
        $validated = $this->validated();

        return [
            'method' => (string) $validated['method'],
            'path' => (string) $validated['path'],
            'accept' => (string) ($validated['accept'] ?? 'text/html'),
        ];
    }
}
