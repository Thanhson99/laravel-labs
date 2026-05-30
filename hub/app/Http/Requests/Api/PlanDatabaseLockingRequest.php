<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates database-locking planning input for the concurrency workbench API.
 */
final class PlanDatabaseLockingRequest extends FormRequest
{
    /**
     * Allow public database-locking planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for database-locking planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'scenario_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'invariant' => ['required', 'string', Rule::in(['inventory', 'balance', 'booking', 'workflow-state'])],
            'resource_type' => ['required', 'string', Rule::in(['product', 'account', 'booking_slot', 'workflow_item'])],
            'concurrent_requests' => ['required', 'integer', 'min:2', 'max:1000'],
            'touches_multiple_rows' => ['required', 'boolean'],
            'has_external_side_effect' => ['required', 'boolean'],
            'expected_failure' => ['required', 'string', Rule::in(['reject', 'retry', 'fail-closed'])],
        ];
    }

    /**
     * Return normalized database-locking planning input.
     *
     * @return array{scenario_name: string, invariant: string, resource_type: string, concurrent_requests: int, touches_multiple_rows: bool, has_external_side_effect: bool, expected_failure: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'scenario_name' => trim((string) $validated['scenario_name']),
            'invariant' => (string) $validated['invariant'],
            'resource_type' => (string) $validated['resource_type'],
            'concurrent_requests' => (int) $validated['concurrent_requests'],
            'touches_multiple_rows' => $this->boolean('touches_multiple_rows'),
            'has_external_side_effect' => $this->boolean('has_external_side_effect'),
            'expected_failure' => (string) $validated['expected_failure'],
        ];
    }
}
