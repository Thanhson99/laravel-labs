<?php

declare(strict_types=1);

namespace App\Http\Requests\Practice;

use Illuminate\Foundation\Http\FormRequest;

final class PracticeLabFilterRequest extends FormRequest
{
    /**
     * Allow public practice pages and APIs to build filtered lab payloads.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate common query filters used by generated practice labs.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'family' => ['nullable', 'string', 'max:80'],
            'language' => ['nullable', 'string', 'max:10'],
            'search' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
            'phase_limit' => ['nullable', 'integer', 'min:1', 'max:10'],
            'tasks_per_phase' => ['nullable', 'integer', 'min:1', 'max:10'],
            'days' => ['nullable', 'integer', 'min:1', 'max:30'],
        ];
    }

    /**
     * Build normalized filters for lab chains that depend on phased practice output.
     *
     * @return array{
     *     family: string,
     *     language: string,
     *     search: string,
     *     limit: int,
     *     phase_limit: int,
     *     tasks_per_phase: int,
     *     days: int
     * }
     */
    public function pipelineFilters(
        int $defaultLimit = 5,
        int $defaultPhaseLimit = 2,
        int $defaultTasksPerPhase = 2,
        int $defaultDays = 5
    ): array {
        return [
            'family' => $this->string('family')->toString() ?: 'laravel',
            'language' => $this->string('language')->toString() ?: 'en',
            'search' => $this->string('search')->toString() ?: 'api',
            'limit' => $this->integer('limit') ?: $defaultLimit,
            'phase_limit' => $this->integer('phase_limit') ?: $defaultPhaseLimit,
            'tasks_per_phase' => $this->integer('tasks_per_phase') ?: $defaultTasksPerPhase,
            'days' => $this->integer('days') ?: $defaultDays,
        ];
    }

    /**
     * Build normalized filters for phased labs that do not use day-based scheduling.
     *
     * @return array{
     *     family: string,
     *     language: string,
     *     search: string,
     *     limit: int,
     *     phase_limit: int,
     *     tasks_per_phase: int
     * }
     */
    public function phasedFilters(
        int $defaultLimit = 5,
        int $defaultPhaseLimit = 2,
        int $defaultTasksPerPhase = 2
    ): array {
        return [
            'family' => $this->string('family')->toString() ?: 'laravel',
            'language' => $this->string('language')->toString() ?: 'en',
            'search' => $this->string('search')->toString() ?: 'api',
            'limit' => $this->integer('limit') ?: $defaultLimit,
            'phase_limit' => $this->integer('phase_limit') ?: $defaultPhaseLimit,
            'tasks_per_phase' => $this->integer('tasks_per_phase') ?: $defaultTasksPerPhase,
        ];
    }
}
