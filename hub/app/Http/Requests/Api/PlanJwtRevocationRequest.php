<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanJwtRevocationRequest extends FormRequest
{
    /**
     * Allow public JWT revocation planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for JWT revocation planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'client_type' => ['required', 'string', Rule::in(['browser-spa', 'mobile-app', 'server-api', 'third-party-api'])],
            'revocation_model' => ['required', 'string', Rule::in(['auto', 'denylist', 'token-version', 'refresh-rotation', 'short-lived-access-token'])],
            'token_lifetime' => ['required', 'string', Rule::in(['minutes', 'hours', 'days'])],
            'revocation_store' => ['required', 'string', Rule::in(['cache', 'database', 'redis'])],
            'immediate_logout' => ['required', 'string', Rule::in(['yes', 'no'])],
            'refresh_rotation' => ['required', 'string', Rule::in(['yes', 'no'])],
        ];
    }

    /**
     * Return normalized JWT revocation planning input.
     *
     * @return array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'client_type' => (string) $validated['client_type'],
            'revocation_model' => (string) $validated['revocation_model'],
            'token_lifetime' => (string) $validated['token_lifetime'],
            'revocation_store' => (string) $validated['revocation_store'],
            'immediate_logout' => (string) $validated['immediate_logout'],
            'refresh_rotation' => (string) $validated['refresh_rotation'],
        ];
    }
}
