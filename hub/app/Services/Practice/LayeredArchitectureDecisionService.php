<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class LayeredArchitectureDecisionService
{
    /**
     * Decide whether a Laravel feature should add architecture layers.
     *
     * @param  array{feature_name: string, feature_type: string, business_rule_count: int, integration_count: int, persistence_complexity: string, requires_async_work: bool, requires_policy: bool}  $input
     * @return array{feature_name: string, recommendation: string, score: int, reason: string, layer_plan: array<int, array{layer: string, use_when: string, skip_when: string, recommendation: string}>, implementation_steps: array<int, string>, review_questions: array<int, string>, anti_patterns: array<int, string>, testing_strategy: array<int, string>, example_structure: array<int, string>, interview_answer: string}
     */
    public function decide(array $input): array
    {
        $featureName = trim($input['feature_name']);
        $score = $this->score($input);
        $recommendation = $this->recommendation($score, $input['feature_type']);

        return [
            'feature_name' => $featureName,
            'recommendation' => $recommendation,
            'score' => $score,
            'reason' => $this->reason($recommendation, $input),
            'layer_plan' => $this->layerPlan($input),
            'implementation_steps' => $this->implementationSteps($recommendation, $featureName, $input),
            'review_questions' => $this->reviewQuestions(),
            'anti_patterns' => $this->antiPatterns(),
            'testing_strategy' => $this->testingStrategy($recommendation, $featureName, $input),
            'example_structure' => $this->exampleStructure($recommendation, $featureName, $input),
            'interview_answer' => $this->interviewAnswer($recommendation),
        ];
    }

    /**
     * Score the architecture pressure signals.
     *
     * @param  array{feature_name: string, feature_type: string, business_rule_count: int, integration_count: int, persistence_complexity: string, requires_async_work: bool, requires_policy: bool}  $input
     */
    private function score(array $input): int
    {
        $score = 0;
        $score += min(4, $input['business_rule_count']);
        $score += $input['integration_count'] * 2;
        $score += match ($input['persistence_complexity']) {
            'none' => 0,
            'simple' => 1,
            'complex' => 3,
            default => 0,
        };
        $score += $input['requires_async_work'] ? 2 : 0;
        $score += $input['requires_policy'] ? 1 : 0;
        $score += $input['feature_type'] === 'workflow' ? 2 : 0;
        $score += $input['feature_type'] === 'integration' ? 3 : 0;

        return $score;
    }

    /**
     * Return the architecture recommendation from score and feature type.
     */
    private function recommendation(int $score, string $featureType): string
    {
        if ($score <= 3 && $featureType === 'crud') {
            return 'keep simple';
        }

        if ($score <= 7) {
            return 'add focused action or service';
        }

        return 'add explicit layers';
    }

    /**
     * Explain the recommendation with practical Laravel language.
     *
     * @param  array{feature_name: string, feature_type: string, business_rule_count: int, integration_count: int, persistence_complexity: string, requires_async_work: bool, requires_policy: bool}  $input
     */
    private function reason(string $recommendation, array $input): string
    {
        return match ($recommendation) {
            'keep simple' => 'The feature has low architecture pressure, so a Form Request, controller, model or query, and view/resource can stay readable without extra ceremony.',
            'add focused action or service' => 'The feature has enough workflow or business-rule pressure to move decisions out of the controller, but it does not need every possible layer.',
            default => sprintf(
                'The feature has strong boundary pressure: %d business rules, %d integrations, %s persistence, async=%s, policy=%s.',
                $input['business_rule_count'],
                $input['integration_count'],
                $input['persistence_complexity'],
                $input['requires_async_work'] ? 'yes' : 'no',
                $input['requires_policy'] ? 'yes' : 'no'
            ),
        };
    }

    /**
     * Build a layer-by-layer plan.
     *
     * @param  array{feature_name: string, feature_type: string, business_rule_count: int, integration_count: int, persistence_complexity: string, requires_async_work: bool, requires_policy: bool}  $input
     * @return array<int, array{layer: string, use_when: string, skip_when: string, recommendation: string}>
     */
    private function layerPlan(array $input): array
    {
        return [
            [
                'layer' => 'Form Request',
                'use_when' => 'Input validation or authorization is more than a trivial required field.',
                'skip_when' => 'There is no user input or validation is already handled by a tiny route parameter.',
                'recommendation' => 'use',
            ],
            [
                'layer' => 'Controller',
                'use_when' => 'Orchestrating request, authorization, action/service call, and response.',
                'skip_when' => 'Never skip for HTTP features, but keep it thin.',
                'recommendation' => 'use thin controller',
            ],
            [
                'layer' => 'Action or Service',
                'use_when' => 'Workflow decisions, transactions, multiple writes, or business rules need a named home.',
                'skip_when' => 'The controller only stores or updates one model with validated data.',
                'recommendation' => $input['business_rule_count'] > 1 || $input['feature_type'] === 'workflow' ? 'use' : 'skip for now',
            ],
            [
                'layer' => 'Repository or Query Object',
                'use_when' => 'Persistence logic is reusable, complex, or must hide data-source details.',
                'skip_when' => 'The query is a simple Eloquent create, find, update, or relationship load.',
                'recommendation' => $input['persistence_complexity'] === 'complex' ? 'use' : 'skip for now',
            ],
            [
                'layer' => 'Policy',
                'use_when' => 'Ownership, role, team, or state-based authorization must be reused or tested clearly.',
                'skip_when' => 'The feature has no user-specific access decision.',
                'recommendation' => $input['requires_policy'] ? 'use' : 'skip for now',
            ],
            [
                'layer' => 'Job or Event',
                'use_when' => 'Slow side effects, retries, notifications, external sync, or post-commit work should leave the request path.',
                'skip_when' => 'The work is fast, local, and must complete before response.',
                'recommendation' => $input['requires_async_work'] || $input['integration_count'] > 0 ? 'consider' : 'skip for now',
            ],
        ];
    }

    /**
     * Return implementation steps for the selected architecture.
     *
     * @param  array{feature_name: string, feature_type: string, business_rule_count: int, integration_count: int, persistence_complexity: string, requires_async_work: bool, requires_policy: bool}  $input
     * @return array<int, string>
     */
    private function implementationSteps(string $recommendation, string $featureName, array $input): array
    {
        $name = Str::studly($featureName);

        if ($recommendation === 'keep simple') {
            return [
                "Create Store{$name}Request only if validation or authorization is meaningful.",
                "Keep {$name}Controller thin and let Eloquent handle the simple write.",
                'Write one feature test for the HTTP behavior.',
                'Do not add service or repository files until a real responsibility appears.',
            ];
        }

        if ($recommendation === 'add focused action or service') {
            return [
                "Create {$name}Action or {$name}Service for workflow decisions.",
                'Wrap multi-step writes in a transaction inside the action/service.',
                'Keep the controller focused on validation, orchestration, and response.',
                'Write a feature test plus a focused service/action test for business decisions.',
            ];
        }

        return [
            "Create Store{$name}Request for validation and authorization.",
            "Create {$name}Controller as a thin orchestrator.",
            "Create {$name}Action or {$name}Service for workflow decisions.",
            $input['persistence_complexity'] === 'complex' ? "Create {$name}Repository or {$name}Query for persistence logic." : 'Keep simple Eloquent queries inside the action/service for now.',
            $input['requires_async_work'] ? "Create {$name}Job or event/listener for slow side effects." : 'Keep side effects synchronous only if they are fast and required before response.',
            'Add tests at the HTTP boundary and the workflow boundary.',
        ];
    }

    /**
     * Return PR review questions for layer decisions.
     *
     * @return array<int, string>
     */
    private function reviewQuestions(): array
    {
        return [
            'Does each new class own a real decision, boundary, or reusable behavior?',
            'Is any layer only forwarding parameters without adding meaning?',
            'Can a reader follow the feature without jumping through unnecessary files?',
            'Are business rules testable without booting unrelated UI or infrastructure?',
            'Is persistence complexity real enough to justify a repository or query object?',
        ];
    }

    /**
     * Return common layering anti-patterns.
     *
     * @return array<int, string>
     */
    private function antiPatterns(): array
    {
        return [
            'Controller calls service, service calls repository, repository calls model, but no layer owns a real decision.',
            'Every simple CRUD screen gets a service and repository only to satisfy a folder pattern.',
            'Business rules are split across many tiny files so the flow is harder to read than before.',
            'Repositories hide Eloquent so completely that useful Laravel features become harder to use.',
            'Layered architecture is claimed as clean architecture even when dependencies still point in confusing directions.',
        ];
    }

    /**
     * Return a testing strategy for the recommendation.
     *
     * @param  array{feature_name: string, feature_type: string, business_rule_count: int, integration_count: int, persistence_complexity: string, requires_async_work: bool, requires_policy: bool}  $input
     * @return array<int, string>
     */
    private function testingStrategy(string $recommendation, string $featureName, array $input): array
    {
        $name = Str::studly($featureName);

        $strategy = [
            "Write a feature test for the {$name} HTTP behavior.",
            'Assert validation failure and successful response behavior.',
        ];

        if ($recommendation !== 'keep simple') {
            $strategy[] = "Write a focused test for {$name}Action or {$name}Service decisions.";
        }

        if ($input['requires_policy']) {
            $strategy[] = 'Add authorization tests for allowed and denied users.';
        }

        if ($input['requires_async_work']) {
            $strategy[] = 'Use queue or event fakes to prove slow side effects are dispatched intentionally.';
        }

        return $strategy;
    }

    /**
     * Return likely files for the recommendation.
     *
     * @param  array{feature_name: string, feature_type: string, business_rule_count: int, integration_count: int, persistence_complexity: string, requires_async_work: bool, requires_policy: bool}  $input
     * @return array<int, string>
     */
    private function exampleStructure(string $recommendation, string $featureName, array $input): array
    {
        $name = Str::studly($featureName);
        $files = [
            "app/Http/Requests/Store{$name}Request.php",
            "app/Http/Controllers/{$name}Controller.php",
            "tests/Feature/{$name}Test.php",
        ];

        if ($recommendation !== 'keep simple') {
            $files[] = "app/Actions/{$name}Action.php";
            $files[] = "tests/Unit/{$name}ActionTest.php";
        }

        if ($input['persistence_complexity'] === 'complex') {
            $files[] = "app/Repositories/{$name}Repository.php";
        }

        if ($input['requires_policy']) {
            $files[] = "app/Policies/{$name}Policy.php";
        }

        if ($input['requires_async_work']) {
            $files[] = "app/Jobs/Run{$name}SideEffects.php";
        }

        return $files;
    }

    /**
     * Return an interview-ready answer about layering.
     */
    private function interviewAnswer(string $recommendation): string
    {
        return match ($recommendation) {
            'keep simple' => 'Layered architecture is not automatically better. For simple CRUD, I keep the Laravel flow direct and add layers only when they reduce coupling or clarify a real decision.',
            'add focused action or service' => 'I would add a focused action or service when workflow decisions start to outgrow the controller, but I would avoid a repository unless persistence complexity is real.',
            default => 'I use explicit layers when there are real boundaries: validation, authorization, business workflow, persistence complexity, async side effects, or integrations. Each layer must own a responsibility, not just forward data.',
        };
    }
}
