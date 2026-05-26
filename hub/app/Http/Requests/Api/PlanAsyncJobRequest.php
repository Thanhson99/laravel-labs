<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class PlanAsyncJobRequest extends FormRequest
{
    /**
     * Allow public async job planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for async job planning input.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'job_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'payload_key' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'backoff_seconds' => ['required', 'integer', 'min:1', 'max:3600'],
        ];
    }

    /**
     * Return normalized async job planning input.
     *
     * @return array{job_name: string, payload_key: string, attempts: int, backoff_seconds: int}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'job_name' => trim((string) $validated['job_name']),
            'payload_key' => trim((string) $validated['payload_key']),
            'attempts' => (int) $validated['attempts'],
            'backoff_seconds' => (int) $validated['backoff_seconds'],
        ];
    }
}
