<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanJwtTokenStorageRequest extends FormRequest
{
    /**
     * Allow public JWT token-storage planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for JWT token-storage planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'client_type' => ['required', 'string', Rule::in(['same-domain-spa', 'cross-domain-spa', 'mobile-app', 'third-party-api', 'low-risk-demo'])],
            'current_storage' => ['required', 'string', Rule::in(['unknown', 'localStorage', 'http-only-cookie', 'memory', 'bearer-token', 'platform-storage'])],
            'token_lifetime' => ['required', 'string', Rule::in(['minutes', 'hours', 'days'])],
            'xss_risk' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'csrf_controls' => ['required', 'string', Rule::in(['none', 'basic', 'strong'])],
            'refresh_token' => ['required', 'string', Rule::in(['yes', 'no'])],
        ];
    }

    /**
     * Return normalized JWT token-storage planning input.
     *
     * @return array{client_type: string, current_storage: string, token_lifetime: string, xss_risk: string, csrf_controls: string, refresh_token: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'client_type' => (string) $validated['client_type'],
            'current_storage' => (string) $validated['current_storage'],
            'token_lifetime' => (string) $validated['token_lifetime'],
            'xss_risk' => (string) $validated['xss_risk'],
            'csrf_controls' => (string) $validated['csrf_controls'],
            'refresh_token' => (string) $validated['refresh_token'],
        ];
    }
}
