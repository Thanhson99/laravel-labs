<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ScoreAiCloudInterviewRubricRequest extends FormRequest
{
    /**
     * Allow public rubric scoring requests from the learning workbench.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for AI Cloud interview rubric input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'candidate_level' => ['required', 'string', Rule::in(['junior', 'mid', 'senior'])],
            'recent_task' => ['required', 'string', Rule::in(['none', 'vague', 'specific'])],
            'prompt_workflow' => ['required', 'string', Rule::in(['none', 'basic', 'structured'])],
            'iac_review' => ['required', 'string', Rule::in(['none', 'basic', 'production'])],
            'failure_story' => ['required', 'string', Rule::in(['none', 'generic', 'concrete'])],
            'verification_workflow' => ['required', 'string', Rule::in(['none', 'manual', 'systematic'])],
            'docs_conflict' => ['required', 'string', Rule::in(['trusts-ai', 'check-docs', 'source-of-truth'])],
            'team_enablement' => ['required', 'string', Rule::in(['none', 'prompt-file', 'team-standard'])],
        ];
    }

    /**
     * Return normalized rubric input.
     *
     * @return array{candidate_level: string, recent_task: string, prompt_workflow: string, iac_review: string, failure_story: string, verification_workflow: string, docs_conflict: string, team_enablement: string}
     */
    public function rubricData(): array
    {
        $validated = $this->validated();

        return [
            'candidate_level' => (string) $validated['candidate_level'],
            'recent_task' => (string) $validated['recent_task'],
            'prompt_workflow' => (string) $validated['prompt_workflow'],
            'iac_review' => (string) $validated['iac_review'],
            'failure_story' => (string) $validated['failure_story'],
            'verification_workflow' => (string) $validated['verification_workflow'],
            'docs_conflict' => (string) $validated['docs_conflict'],
            'team_enablement' => (string) $validated['team_enablement'],
        ];
    }
}
