<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanGraphqlRestDecisionRequest extends FormRequest
{
    /**
     * Allow public API design planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for REST versus GraphQL planning.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'client_type' => ['required', 'string', Rule::in(['public-api', 'mobile-app', 'spa-dashboard', 'internal-admin', 'bff'])],
            'data_shape' => ['required', 'string', Rule::in(['resource-crud', 'screen-composition', 'graph-shaped', 'reporting'])],
            'field_flexibility' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'cache_priority' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'relationship_depth' => ['required', 'string', Rule::in(['shallow', 'moderate', 'deep'])],
            'team_graphql_experience' => ['required', 'string', Rule::in(['none', 'some', 'strong'])],
            'authorization_complexity' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
        ];
    }

    /**
     * Return normalized planning input.
     *
     * @return array{client_type: string, data_shape: string, field_flexibility: string, cache_priority: string, relationship_depth: string, team_graphql_experience: string, authorization_complexity: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'client_type' => (string) $validated['client_type'],
            'data_shape' => (string) $validated['data_shape'],
            'field_flexibility' => (string) $validated['field_flexibility'],
            'cache_priority' => (string) $validated['cache_priority'],
            'relationship_depth' => (string) $validated['relationship_depth'],
            'team_graphql_experience' => (string) $validated['team_graphql_experience'],
            'authorization_complexity' => (string) $validated['authorization_complexity'],
        ];
    }
}
