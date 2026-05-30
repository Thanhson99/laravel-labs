<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanLsmTreeRequest extends FormRequest
{
    /**
     * Allow public LSM Tree planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for LSM Tree planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'workload_pattern' => ['required', 'string', Rule::in(['write-heavy', 'read-heavy', 'mixed'])],
            'write_rate_per_second' => ['required', 'integer', 'min:1', 'max:1000000'],
            'read_miss_ratio' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'compaction_strategy' => ['required', 'string', Rule::in(['size-tiered', 'leveled', 'universal'])],
            'bloom_filter_enabled' => ['required', 'string', Rule::in(['yes', 'no'])],
            'schema_expectation' => ['required', 'string', Rule::in(['schema-flexible', 'schema-strict'])],
        ];
    }

    /**
     * Return normalized LSM Tree planning input.
     *
     * @return array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'workload_pattern' => (string) $validated['workload_pattern'],
            'write_rate_per_second' => (int) $validated['write_rate_per_second'],
            'read_miss_ratio' => (string) $validated['read_miss_ratio'],
            'compaction_strategy' => (string) $validated['compaction_strategy'],
            'bloom_filter_enabled' => (string) $validated['bloom_filter_enabled'],
            'schema_expectation' => (string) $validated['schema_expectation'],
        ];
    }
}
