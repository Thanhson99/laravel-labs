<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class AuthorizationPolicyPlanService
{
    /**
     * Build a policy authorization plan for access-control practice.
     *
     * @param  array{model_name: string, ability: string, actor_role: string, rule: string}  $input
     * @return array{policy: array<string, string>, controller_usage: string, decision_rule: array<string, string>, tests: array<int, string>, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $model = Str::studly($input['model_name']);
        $ability = Str::camel($input['ability']);
        $role = Str::slug($input['actor_role']);

        return [
            'policy' => [
                'class' => $model.'Policy',
                'method' => $ability,
                'model' => $model,
            ],
            'controller_usage' => "\$this->authorize('{$ability}', \${$this->variableName($model)});",
            'decision_rule' => [
                'actor_role' => $role,
                'rule' => trim($input['rule']),
            ],
            'tests' => [
                'Allowed actor receives a successful response.',
                'Forbidden actor receives HTTP 403.',
                'Controller calls authorization before mutating state.',
                'Policy rule is tested without duplicating controller behavior.',
            ],
            'commands' => [
                'php artisan make:policy '.$model.'Policy --model='.$model,
                'php artisan test --filter AuthorizationPolicyPlanWorkbenchTest',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return a conventional variable name for a model class.
     */
    private function variableName(string $model): string
    {
        return Str::camel($model);
    }
}
