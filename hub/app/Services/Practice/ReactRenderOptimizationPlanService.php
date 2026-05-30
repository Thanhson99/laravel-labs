<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class ReactRenderOptimizationPlanService
{
    /**
     * Build a React render optimization plan for memo, useMemo, and useCallback practice.
     *
     * @param  array{component_name: string, component_type: string, render_issue: string, state_shape: string, list_size: int, profiler_signal: string}  $input
     * @return array<string, mixed>
     */
    public function plan(array $input): array
    {
        $component = Str::studly($input['component_name']);
        $risk = $this->riskLevel($input);
        $recommendation = $this->recommendation($input);
        $measurementTemplate = $this->measurementTemplate($input, $component);
        $dependencyReview = $this->dependencyReview($input);
        $pullRequestNote = $this->pullRequestNote($input, $component, $recommendation);

        return [
            'component' => $component,
            'risk_level' => $risk,
            'readiness_score' => $this->readinessScore($input, $risk),
            'recommendation' => $recommendation,
            'decision_tree' => $this->decisionTree($input),
            'tool_decision_matrix' => $this->toolDecisionMatrix($input),
            'optimization_plan' => $this->optimizationPlan($input),
            'implementation_steps' => $this->implementationSteps($input),
            'anti_patterns' => $this->antiPatterns(),
            'profiler_checklist' => $this->profilerChecklist($input),
            'measurement_template' => $measurementTemplate,
            'dependency_review' => $dependencyReview,
            'regression_checks' => $this->regressionChecks($input),
            'code_examples' => $this->codeExamples($input),
            'review_checklist' => $this->reviewChecklist(),
            'pull_request_note' => $pullRequestNote,
            'review_packet_markdown' => $this->reviewPacketMarkdown($component, $recommendation, $measurementTemplate, $dependencyReview, $pullRequestNote),
            'interview_answer' => $this->interviewAnswer($input, $recommendation),
            'commands' => [
                'php artisan test --filter ReactRenderOptimizationPlan',
                'php artisan route:list --path=react-render-optimization-plan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * @param  array{render_issue: string, list_size: int, profiler_signal: string}  $input
     */
    private function riskLevel(array $input): string
    {
        $score = 0;
        $score += $input['render_issue'] === 'large-list' ? 3 : 0;
        $score += $input['list_size'] >= 500 ? 3 : ($input['list_size'] >= 100 ? 1 : 0);
        $score += $input['profiler_signal'] === 'slow-commit' ? 2 : 0;
        $score += $input['profiler_signal'] === 'prop-churn' ? 1 : 0;

        return match (true) {
            $score >= 6 => 'high',
            $score >= 3 => 'medium',
            default => 'low',
        };
    }

    /**
     * @param  array{render_issue: string, profiler_signal: string, list_size: int}  $input
     * @return array{score: int, label: string, blockers: array<int, string>, next_actions: array<int, string>}
     */
    private function readinessScore(array $input, string $risk): array
    {
        $score = 80;
        $blockers = [];

        if ($input['profiler_signal'] === 'not-measured') {
            $score -= 35;
            $blockers[] = 'No React Profiler signal has been captured yet.';
        }

        if ($risk === 'high') {
            $score -= 15;
            $blockers[] = 'High render risk needs before/after evidence and a rollback path.';
        }

        if ($input['render_issue'] === 'large-list' && $input['list_size'] >= 500) {
            $score -= 10;
            $blockers[] = 'Large list optimization needs pagination or virtualization review.';
        }

        $score = max(0, min(100, $score));

        return [
            'score' => $score,
            'label' => match (true) {
                $score >= 80 => 'ready',
                $score >= 60 => 'needs-evidence',
                default => 'blocked',
            },
            'blockers' => $blockers,
            'next_actions' => $this->readinessNextActions($input, $blockers),
        ];
    }

    /**
     * @param  array{render_issue: string, profiler_signal: string, list_size: int}  $input
     * @param  array<int, string>  $blockers
     * @return array<int, string>
     */
    private function readinessNextActions(array $input, array $blockers): array
    {
        if ($blockers === []) {
            return [
                'Apply the smallest change and keep the before/after profiler note in the PR.',
                'Review dependency arrays before merging.',
            ];
        }

        $actions = [];

        if ($input['profiler_signal'] === 'not-measured') {
            $actions[] = 'Capture a React Profiler baseline for the target interaction.';
        }

        if ($input['render_issue'] === 'large-list' && $input['list_size'] >= 500) {
            $actions[] = 'Evaluate virtualization or pagination before adding more memoization.';
        }

        $actions[] = 'Regenerate the plan after evidence is available.';

        return $actions;
    }

    /**
     * @param  array{render_issue: string, state_shape: string, list_size: int, profiler_signal: string}  $input
     */
    private function recommendation(array $input): string
    {
        if ($input['render_issue'] === 'large-list' && $input['list_size'] >= 500) {
            return 'Start with list virtualization and stable item props; memo alone will not save a very large rendered list.';
        }

        if ($input['profiler_signal'] === 'prop-churn') {
            return 'Stabilize object, array, and callback props with useMemo or useCallback, then wrap expensive children with React.memo.';
        }

        if ($input['render_issue'] === 'expensive-calculation') {
            return 'Move the expensive derived calculation behind useMemo, verify dependencies, and keep the calculation outside child render loops when possible.';
        }

        if ($input['render_issue'] === 'context-update') {
            return 'Split broad context updates or select smaller state slices before memoizing children that re-render from provider changes.';
        }

        if ($input['state_shape'] === 'global-state') {
            return 'Reduce state blast radius first: move state closer, split context, or select smaller slices before adding memoization.';
        }

        return 'Measure first with React Profiler, then use memoization only around expensive children, derived values, or unstable callbacks.';
    }

    /**
     * @param  array{render_issue: string}  $input
     * @return array<int, array{question: string, choose: string}>
     */
    private function decisionTree(array $input): array
    {
        return [
            [
                'question' => 'Is the component actually slow in React Profiler?',
                'choose' => 'If no, do not add memoization just because a render happened.',
            ],
            [
                'question' => 'Are child props recreated on every render?',
                'choose' => 'Use useMemo for object/array props and useCallback for function props when the child is memoized.',
            ],
            [
                'question' => 'Is the expensive work a derived calculation?',
                'choose' => 'Use useMemo for the calculation, but keep dependencies minimal and correct.',
            ],
            [
                'question' => "Is the issue {$input['render_issue']}?",
                'choose' => 'Fix the structural cause first: state placement, context splitting, key stability, or list virtualization.',
            ],
        ];
    }

    /**
     * @param  array{render_issue: string, state_shape: string, list_size: int, profiler_signal: string}  $input
     * @return array<int, array{tool: string, fit: string, score: int, use_when: string, avoid_when: string}>
     */
    private function toolDecisionMatrix(array $input): array
    {
        $memoScore = $input['profiler_signal'] === 'prop-churn' ? 85 : 55;
        $useMemoScore = $input['render_issue'] === 'expensive-calculation' ? 90 : ($input['profiler_signal'] === 'prop-churn' ? 75 : 50);
        $useCallbackScore = $input['profiler_signal'] === 'prop-churn' ? 80 : 45;
        $stateScore = in_array($input['state_shape'], ['context-state', 'global-state'], true) ? 88 : 60;
        $virtualizationScore = $input['list_size'] >= 500 ? 95 : 25;

        return [
            [
                'tool' => 'React.memo',
                'fit' => $this->fitLabel($memoScore),
                'score' => $memoScore,
                'use_when' => 'A child is expensive and receives stable props most of the time.',
                'avoid_when' => 'The component is cheap or props are recreated every parent render.',
            ],
            [
                'tool' => 'useMemo',
                'fit' => $this->fitLabel($useMemoScore),
                'score' => $useMemoScore,
                'use_when' => 'A derived value is expensive or object/array identity must stay stable.',
                'avoid_when' => 'The calculation is cheap or dependencies are unclear.',
            ],
            [
                'tool' => 'useCallback',
                'fit' => $this->fitLabel($useCallbackScore),
                'score' => $useCallbackScore,
                'use_when' => 'A callback is passed to a memoized child or dependency-sensitive hook.',
                'avoid_when' => 'The handler stays local and no memoized child depends on its identity.',
            ],
            [
                'tool' => 'state locality / context split',
                'fit' => $this->fitLabel($stateScore),
                'score' => $stateScore,
                'use_when' => 'State or context updates are causing too many unrelated children to render.',
                'avoid_when' => 'The state is already local and the measured issue is a small derived calculation.',
            ],
            [
                'tool' => 'virtualization / pagination',
                'fit' => $this->fitLabel($virtualizationScore),
                'score' => $virtualizationScore,
                'use_when' => 'The UI mounts too many rows, cards, or DOM nodes at once.',
                'avoid_when' => 'The list is small and the measured issue is prop churn or callback identity.',
            ],
        ];
    }

    /**
     * Convert a numeric fit score into a readable label.
     */
    private function fitLabel(int $score): string
    {
        return match (true) {
            $score >= 85 => 'strong',
            $score >= 65 => 'situational',
            default => 'weak',
        };
    }

    /**
     * @param  array{component_type: string, render_issue: string, list_size: int}  $input
     * @return array<int, array{tool: string, when_to_use: string, caution: string}>
     */
    private function optimizationPlan(array $input): array
    {
        return [
            [
                'tool' => 'React.memo',
                'when_to_use' => "Use for expensive {$input['component_type']} children whose props usually stay the same.",
                'caution' => 'It is useless when props always change or the component is cheap.',
            ],
            [
                'tool' => 'useMemo',
                'when_to_use' => 'Use for expensive derived values or stable object/array props passed to memoized children.',
                'caution' => 'Wrong dependencies create stale UI; cheap calculations do not need it.',
            ],
            [
                'tool' => 'useCallback',
                'when_to_use' => 'Use for callbacks passed to memoized children or hooks that depend on function identity.',
                'caution' => 'It does not make the function faster; it only stabilizes identity.',
            ],
            [
                'tool' => $input['list_size'] >= 500 ? 'virtualization' : 'state locality',
                'when_to_use' => $input['list_size'] >= 500 ? 'Use when too many DOM rows are rendered at once.' : 'Keep state near the component that needs it.',
                'caution' => 'Memoization cannot compensate for rendering the wrong amount of UI or updating too much state.',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function antiPatterns(): array
    {
        return [
            'Wrapping every component in React.memo without measuring.',
            'Using useCallback for local handlers that are not passed to memoized children.',
            'Putting a new object or array literal into props and expecting React.memo to help.',
            'Using useMemo to hide expensive render caused by state living too high in the tree.',
        ];
    }

    /**
     * @param  array{render_issue: string, state_shape: string, list_size: int}  $input
     * @return array<int, array{step: string, action: string, verify: string}>
     */
    private function implementationSteps(array $input): array
    {
        $structuralStep = match ($input['render_issue']) {
            'large-list' => [
                'step' => 'Reduce rendered DOM work',
                'action' => 'Add pagination or virtualization before relying on memoization.',
                'verify' => 'Profiler shows fewer mounted rows and shorter commits for the same list size.',
            ],
            'context-update' => [
                'step' => 'Reduce context blast radius',
                'action' => 'Split provider values or introduce selectors so unrelated consumers do not update together.',
                'verify' => 'Profiler shows fewer children rendering after the same context change.',
            ],
            'expensive-calculation' => [
                'step' => 'Isolate expensive derivation',
                'action' => 'Wrap the derived calculation in useMemo or precompute it before rendering child lists.',
                'verify' => 'Profiler shows less render time without stale derived values.',
            ],
            default => [
                'step' => 'Stabilize render inputs',
                'action' => 'Stabilize object, array, and callback props only where memoized children depend on identity.',
                'verify' => 'Profiler shows the expensive child skips renders when inputs are unchanged.',
            ],
        };

        return [
            [
                'step' => 'Capture baseline',
                'action' => 'Record the target interaction in React Profiler before changing code.',
                'verify' => 'Baseline includes commit duration, rendered components, and the suspected trigger.',
            ],
            $structuralStep,
            [
                'step' => 'Apply the smallest memoization boundary',
                'action' => $input['state_shape'] === 'local-state'
                    ? 'Keep state local and memoize only expensive derived values or children.'
                    : 'Move state closer or split subscriptions before adding React.memo everywhere.',
                'verify' => 'The change fixes the measured issue without broad stale-closure risk.',
            ],
            [
                'step' => 'Capture after evidence',
                'action' => 'Repeat the same profiler interaction with the same data shape.',
                'verify' => 'Before/after evidence supports the PR note and review packet.',
            ],
        ];
    }

    /**
     * @param  array{profiler_signal: string}  $input
     * @return array<int, string>
     */
    private function profilerChecklist(array $input): array
    {
        return [
            "Start from the profiler signal: {$input['profiler_signal']}.",
            'Record before/after commit duration and rendered component count.',
            'Check which props changed and whether the change was meaningful.',
            'Verify optimization did not introduce stale closures or stale derived data.',
        ];
    }

    /**
     * @param  array{render_issue: string, profiler_signal: string, list_size: int}  $input
     * @return array{component: string, before: array<int, string>, after: array<int, string>, pass_condition: string}
     */
    private function measurementTemplate(array $input, string $component): array
    {
        return [
            'component' => $component,
            'before' => [
                "Profiler signal observed: {$input['profiler_signal']}.",
                "Render issue suspected: {$input['render_issue']}.",
                "Rendered list size at measurement time: {$input['list_size']}.",
            ],
            'after' => [
                'Record the same interaction with the same data size.',
                'Compare commit duration, rendered component count, and meaningful prop changes.',
                'Keep the change only if the measured improvement is visible and code stays readable.',
            ],
            'pass_condition' => 'The optimized interaction renders fewer expensive components or shorter commits without stale UI.',
        ];
    }

    /**
     * @param  array{render_issue: string, state_shape: string, list_size: int}  $input
     * @return array<int, array{target: string, check: string, failure_mode: string}>
     */
    private function dependencyReview(array $input): array
    {
        return [
            [
                'target' => 'React.memo props',
                'check' => 'Confirm object, array, and callback props are stable or intentionally changing.',
                'failure_mode' => 'Memoized child still renders because parent creates new props every render.',
            ],
            [
                'target' => 'useMemo dependencies',
                'check' => "Include every value used by the derived calculation for {$input['render_issue']}.",
                'failure_mode' => 'Missing dependency produces stale derived UI.',
            ],
            [
                'target' => 'useCallback dependencies',
                'check' => 'Keep callback dependencies complete; use functional state updates when they reduce dependency churn safely.',
                'failure_mode' => 'Stale closure reads an old state value or forces children to re-render anyway.',
            ],
            [
                'target' => $input['list_size'] >= 500 ? 'list rendering boundary' : 'state owner',
                'check' => $input['list_size'] >= 500 ? 'Evaluate virtualization or pagination before adding more memoization.' : "Check whether {$input['state_shape']} updates can move closer to the component that needs them.",
                'failure_mode' => 'Memoization hides a structural render problem instead of fixing it.',
            ],
        ];
    }

    /**
     * @param  array{render_issue: string, state_shape: string, list_size: int}  $input
     * @return array<int, array{name: string, purpose: string, check: string}>
     */
    private function regressionChecks(array $input): array
    {
        $checks = [
            [
                'name' => 'stale-derived-data',
                'purpose' => 'Prove memoized values update when real inputs change.',
                'check' => 'Change the query/filter/input data and verify the visible UI updates immediately.',
            ],
            [
                'name' => 'callback-fresh-state',
                'purpose' => 'Prove callbacks do not capture old state.',
                'check' => 'Trigger the callback after state changes and verify it reads the latest selected item or form value.',
            ],
            [
                'name' => 'memoized-child-props',
                'purpose' => 'Prove skipped renders are intentional.',
                'check' => 'Change a meaningful child prop and verify the child still renders with the new value.',
            ],
        ];

        if ($input['render_issue'] === 'large-list' || $input['list_size'] >= 500) {
            $checks[] = [
                'name' => 'virtualized-list-visibility',
                'purpose' => 'Prove virtualization or pagination does not hide reachable records.',
                'check' => 'Scroll or page through the list and verify first, middle, and last records remain reachable.',
            ];
        }

        if (in_array($input['state_shape'], ['context-state', 'global-state'], true)) {
            $checks[] = [
                'name' => 'context-subscriber-scope',
                'purpose' => 'Prove splitting context or selecting state slices did not orphan subscribers.',
                'check' => 'Update the provider/global state and verify every screen that should update still does.',
            ];
        }

        return $checks;
    }

    /**
     * @param  array{component_name: string}  $input
     * @return array<string, string>
     */
    private function codeExamples(array $input): array
    {
        $name = Str::studly($input['component_name']);

        return [
            'memo_child' => "const {$name}Item = React.memo(function {$name}Item({ item, onSelect }) {\n  return <button onClick={() => onSelect(item.id)}>{item.name}</button>;\n});",
            'stable_props' => "const visibleItems = useMemo(() => filterItems(items, query), [items, query]);\nconst handleSelect = useCallback((id) => setSelectedId(id), []);",
            'profiler_note' => '<Profiler id="SearchPanel" onRender={(id, phase, actualDuration) => console.log(id, phase, actualDuration)} />',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function reviewChecklist(): array
    {
        return [
            'Profiler evidence exists before and after the change.',
            'Dependencies in useMemo and useCallback are complete and intentional.',
            'Memoized child props are stable and small.',
            'Large lists use pagination or virtualization before relying on memo.',
            'The code remains readable; optimization did not make simple UI harder to maintain.',
        ];
    }

    /**
     * @param  array{render_issue: string, state_shape: string, list_size: int}  $input
     */
    private function pullRequestNote(array $input, string $component, string $recommendation): string
    {
        return implode("\n", [
            "React render optimization for {$component}.",
            "Measured issue: {$input['render_issue']} with {$input['state_shape']} and list size {$input['list_size']}.",
            "Decision: {$recommendation}",
            'Evidence required: before/after React Profiler capture, dependency review, and stale-UI check.',
        ]);
    }

    /**
     * Build a Markdown packet that can be pasted into a PR or practice note.
     *
     * @param  array{component: string, before: array<int, string>, after: array<int, string>, pass_condition: string}  $measurementTemplate
     * @param  array<int, array{target: string, check: string, failure_mode: string}>  $dependencyReview
     */
    private function reviewPacketMarkdown(string $component, string $recommendation, array $measurementTemplate, array $dependencyReview, string $pullRequestNote): string
    {
        $before = collect($measurementTemplate['before'])
            ->map(fn (string $item): string => "- {$item}")
            ->implode("\n");
        $after = collect($measurementTemplate['after'])
            ->map(fn (string $item): string => "- {$item}")
            ->implode("\n");
        $dependencies = collect($dependencyReview)
            ->map(fn (array $item): string => "- **{$item['target']}**: {$item['check']} Risk: {$item['failure_mode']}")
            ->implode("\n");

        return <<<MARKDOWN
# React Render Review Packet: {$component}

## Decision
{$recommendation}

## Measurement
Before:
{$before}

After:
{$after}

Pass condition: {$measurementTemplate['pass_condition']}

## Dependency Review
{$dependencies}

## PR Note
{$pullRequestNote}
MARKDOWN;
    }

    /**
     * @param  array{render_issue: string}  $input
     */
    private function interviewAnswer(array $input, string $recommendation): string
    {
        return "React re-render optimization starts with measurement. I would use React Profiler to confirm {$input['render_issue']}, then choose the smallest fix: React.memo for expensive children with stable props, useMemo for expensive derived values or stable object props, and useCallback for callback identity passed into memoized children. {$recommendation}";
    }
}
