<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates graph traversal planning input for the BFS/DFS workbench API.
 */
final class PlanGraphTraversalRequest extends FormRequest
{
    /**
     * Allow public graph traversal planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for BFS/DFS planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'scenario_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'goal' => ['required', 'string', Rule::in(['nearest-match', 'shortest-path', 'branch-exploration', 'dependency-reasoning', 'subtree-validation'])],
            'graph_shape' => ['required', 'string', Rule::in(['wide', 'deep', 'cyclic', 'tree', 'api-links'])],
            'node_count' => ['required', 'integer', 'min:2', 'max:100000'],
            'max_depth' => ['required', 'integer', 'min:1', 'max:50'],
            'weighted_edges' => ['required', 'boolean'],
            'production_context' => ['required', 'string', Rule::in(['api-crawling', 'database-hierarchy', 'dependency-graph', 'menu-rendering'])],
        ];
    }

    /**
     * Return normalized BFS/DFS planning input.
     *
     * @return array{scenario_name: string, goal: string, graph_shape: string, node_count: int, max_depth: int, weighted_edges: bool, production_context: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'scenario_name' => trim((string) $validated['scenario_name']),
            'goal' => (string) $validated['goal'],
            'graph_shape' => (string) $validated['graph_shape'],
            'node_count' => (int) $validated['node_count'],
            'max_depth' => (int) $validated['max_depth'],
            'weighted_edges' => $this->boolean('weighted_edges'),
            'production_context' => (string) $validated['production_context'],
        ];
    }
}
