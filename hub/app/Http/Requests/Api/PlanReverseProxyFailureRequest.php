<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanReverseProxyFailureRequest extends FormRequest
{
    /**
     * Allow public reverse-proxy failure planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for reverse-proxy failure planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'service_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'proxy_layer' => ['required', 'string', Rule::in(['edge-cdn', 'regional-proxy', 'internal-nginx'])],
            'change_type' => ['required', 'string', Rule::in(['config-file', 'feature-file', 'routing-rule', 'waf-rule', 'deploy'])],
            'origin_count' => ['required', 'integer', 'min:1', 'max:500'],
            'rollout_strategy' => ['required', 'string', Rule::in(['global', 'staged', 'canary'])],
            'has_health_gate' => ['required', 'boolean'],
            'fail_behavior' => ['required', 'string', Rule::in(['fail_closed', 'fail_open', 'serve_stale', 'bypass_optional_module'])],
            'observed_failure' => ['required', 'string', Rule::in(['http_500', 'timeouts', 'tls_loop', 'bad_routing'])],
        ];
    }

    /**
     * Return normalized reverse-proxy failure planning input.
     *
     * @return array{service_name: string, proxy_layer: string, change_type: string, origin_count: int, rollout_strategy: string, has_health_gate: bool, fail_behavior: string, observed_failure: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'service_name' => trim((string) $validated['service_name']),
            'proxy_layer' => (string) $validated['proxy_layer'],
            'change_type' => (string) $validated['change_type'],
            'origin_count' => (int) $validated['origin_count'],
            'rollout_strategy' => (string) $validated['rollout_strategy'],
            'has_health_gate' => $this->boolean('has_health_gate'),
            'fail_behavior' => (string) $validated['fail_behavior'],
            'observed_failure' => (string) $validated['observed_failure'],
        ];
    }
}
