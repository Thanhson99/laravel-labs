<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanLoadBalancerRequest extends FormRequest
{
    /**
     * Allow public load-balancer planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for load-balancer planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'service_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'traffic_pattern' => ['required', 'string', Rule::in(['even', 'heterogeneous', 'bursty', 'session-affinity'])],
            'algorithm' => ['required', 'string', Rule::in(['auto', 'round_robin', 'weighted_round_robin', 'least_connections', 'ip_hash'])],
            'upstream_count' => ['required', 'integer', 'min:2', 'max:8'],
            'has_sticky_sessions' => ['required', 'boolean'],
            'health_check_path' => ['required', 'string', 'min:2', 'max:80', 'regex:/^\/[A-Za-z0-9_\/-]*$/'],
        ];
    }

    /**
     * Return normalized load-balancer planning input.
     *
     * @return array{service_name: string, traffic_pattern: string, algorithm: string, upstream_count: int, has_sticky_sessions: bool, health_check_path: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'service_name' => trim((string) $validated['service_name']),
            'traffic_pattern' => (string) $validated['traffic_pattern'],
            'algorithm' => (string) $validated['algorithm'],
            'upstream_count' => (int) $validated['upstream_count'],
            'has_sticky_sessions' => $this->boolean('has_sticky_sessions'),
            'health_check_path' => trim((string) $validated['health_check_path']),
        ];
    }
}
