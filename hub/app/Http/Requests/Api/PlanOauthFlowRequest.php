<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanOauthFlowRequest extends FormRequest
{
    /**
     * Allow public OAuth flow planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for OAuth flow planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'client_type' => ['required', 'string', Rule::in(['browser-spa', 'server-rendered-web', 'mobile-app', 'legacy-spa', 'service-to-service'])],
            'current_flow' => ['required', 'string', Rule::in(['implicit', 'authorization-code', 'authorization-code-pkce', 'client-credentials', 'unknown'])],
            'can_keep_secret' => ['required', 'string', Rule::in(['yes', 'no'])],
            'pkce_supported' => ['required', 'string', Rule::in(['yes', 'no'])],
            'refresh_token_needed' => ['required', 'string', Rule::in(['yes', 'no'])],
            'browser_history_risk' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
        ];
    }

    /**
     * Return normalized OAuth flow planning input.
     *
     * @return array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'client_type' => (string) $validated['client_type'],
            'current_flow' => (string) $validated['current_flow'],
            'can_keep_secret' => (string) $validated['can_keep_secret'],
            'pkce_supported' => (string) $validated['pkce_supported'],
            'refresh_token_needed' => (string) $validated['refresh_token_needed'],
            'browser_history_risk' => (string) $validated['browser_history_risk'],
        ];
    }
}
