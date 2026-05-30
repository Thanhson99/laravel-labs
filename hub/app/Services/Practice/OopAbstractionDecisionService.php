<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class OopAbstractionDecisionService
{
    /**
     * Decide whether a design needs an interface, abstract class, or concrete class.
     *
     * @param  array{scenario: string, relationship: string, shared_behavior: string, needs_multiple_implementations: bool, has_shared_state: bool}  $input
     * @return array{scenario: string, recommendation: string, reason: string, interview_answer: string, interview_rubric: array{opening: string, contrast: string, example: string, caution: string}, code_example: string, comparison_table: array<int, array{choice: string, use_when: string, avoid_when: string, laravel_example: string}>, decision_matrix: array<int, array{signal: string, value: string, impact: string}>, implementation_plan: array{files: array<int, string>, steps: array<int, string>, review: array<int, string>}, tradeoffs: array<int, string>, anti_patterns: array<int, string>, testing_strategy: array<int, string>, checklist: array<int, string>}
     */
    public function decide(array $input): array
    {
        $scenario = trim($input['scenario']);
        $relationship = $input['relationship'];
        $sharedBehavior = trim($input['shared_behavior']);
        $needsMultipleImplementations = $input['needs_multiple_implementations'];
        $hasSharedState = $input['has_shared_state'];
        $recommendation = $this->recommendation($relationship, $sharedBehavior, $needsMultipleImplementations, $hasSharedState);

        return [
            'scenario' => $scenario,
            'recommendation' => $recommendation,
            'reason' => $this->reason($recommendation, $relationship, $sharedBehavior, $needsMultipleImplementations, $hasSharedState),
            'interview_answer' => $this->interviewAnswer($recommendation),
            'interview_rubric' => $this->interviewRubric($recommendation, $scenario),
            'code_example' => $this->codeExample($recommendation, $scenario),
            'comparison_table' => $this->comparisonTable(),
            'decision_matrix' => $this->decisionMatrix($relationship, $sharedBehavior, $needsMultipleImplementations, $hasSharedState),
            'implementation_plan' => $this->implementationPlan($recommendation, $scenario),
            'tradeoffs' => $this->tradeoffs($recommendation),
            'anti_patterns' => $this->antiPatterns($recommendation),
            'testing_strategy' => $this->testingStrategy($recommendation, $scenario),
            'checklist' => [
                'Can unrelated classes implement this behavior?',
                'Is there real shared state or implementation, not only a similar method name?',
                'Will the abstraction make testing or replacement simpler?',
                'Can you explain why this is not needless indirection?',
            ],
        ];
    }

    /**
     * Return a structured answer shape for interview practice.
     *
     * @return array{opening: string, contrast: string, example: string, caution: string}
     */
    private function interviewRubric(string $recommendation, string $scenario): array
    {
        $name = Str::studly($scenario);

        return match ($recommendation) {
            'interface' => [
                'opening' => 'Start by saying an interface is a contract for behavior, not shared implementation.',
                'contrast' => 'Mention that PHP classes can implement multiple interfaces but extend only one parent class.',
                'example' => "Use {$name}Contract when callers should work with any compatible implementation.",
                'caution' => 'Do not create an interface for every class before there is a replacement, fake, or architectural boundary.',
            ],
            'abstract class' => [
                'opening' => 'Start by saying an abstract class is useful when a family of classes shares base behavior or state.',
                'contrast' => 'Mention that it can contain implemented methods, protected helpers, properties, and abstract methods.',
                'example' => "Use Base{$name} when child classes share stable setup but still need custom behavior.",
                'caution' => 'Avoid inheritance when classes only look similar but do not represent the same parent concept.',
            ],
            default => [
                'opening' => 'Start by saying simple concrete code is often the right first choice.',
                'contrast' => 'Mention that abstractions should be extracted when the need is visible, not guessed too early.',
                'example' => "Keep {$name} concrete until another implementation, shared base behavior, or testing need appears.",
                'caution' => 'Do not add indirection only to make the design look more advanced.',
            ],
        };
    }

    /**
     * Return concrete files and steps for the selected abstraction.
     *
     * @return array{files: array<int, string>, steps: array<int, string>, review: array<int, string>}
     */
    private function implementationPlan(string $recommendation, string $scenario): array
    {
        $name = Str::studly($scenario);

        return match ($recommendation) {
            'interface' => [
                'files' => [
                    "app/Contracts/{$name}Contract.php",
                    "app/Services/{$name}Implementation.php",
                    'app/Providers/AppServiceProvider.php',
                    "tests/Fakes/Fake{$name}.php",
                ],
                'steps' => [
                    "Create {$name}Contract with only the behavior callers need.",
                    "Make the concrete {$name} implementation satisfy the contract.",
                    "Bind {$name}Contract to the concrete implementation in a service provider.",
                    "Type-hint {$name}Contract in the caller and swap it with Fake{$name} in tests.",
                ],
                'review' => [
                    'The interface name describes behavior, not a vendor or framework detail.',
                    'No caller still type-hints the old concrete implementation when a contract is required.',
                    'A fake or second implementation proves the interface is earning its cost.',
                ],
            ],
            'abstract class' => [
                'files' => [
                    "app/Support/Base{$name}.php",
                    "app/Services/{$name}Variant.php",
                    "tests/Unit/Base{$name}Test.php",
                ],
                'steps' => [
                    "Move only stable shared state or base behavior into Base{$name}.",
                    'Keep variant-specific behavior abstract or implemented in child classes.',
                    'Create a small test subclass when shared base behavior needs direct verification.',
                ],
                'review' => [
                    'The base class models a real parent concept.',
                    'Shared mutable state is intentional and easy to reason about.',
                    'Child classes are not forced into inheritance only to reuse one helper method.',
                ],
            ],
            default => [
                'files' => [
                    "app/Services/{$name}.php",
                    "tests/Unit/{$name}Test.php",
                ],
                'steps' => [
                    "Keep {$name} concrete and focused on one responsibility.",
                    'Write direct tests for the current behavior before extracting an abstraction.',
                    'Revisit the design only when a second implementation or stable shared base behavior appears.',
                ],
                'review' => [
                    'The class has one clear responsibility.',
                    'No abstraction was added only for future guesses.',
                    'Tests describe behavior so extraction remains possible later.',
                ],
            ],
        };
    }

    /**
     * Return a compact comparison table for interview and implementation review.
     *
     * @return array<int, array{choice: string, use_when: string, avoid_when: string, laravel_example: string}>
     */
    private function comparisonTable(): array
    {
        return [
            [
                'choice' => 'interface',
                'use_when' => 'Unrelated classes must provide the same behavior, or callers need swappable implementations.',
                'avoid_when' => 'There is only one stable implementation and no test fake or vendor swap is expected.',
                'laravel_example' => 'Bind PaymentGatewayContract to StripePaymentGateway in a service provider.',
            ],
            [
                'choice' => 'abstract class',
                'use_when' => 'A same-family group needs shared base state, template methods, or common helper behavior.',
                'avoid_when' => 'Classes only look similar but do not share a real parent concept.',
                'laravel_example' => 'Create BaseImportJob when several import jobs share file parsing and retry metadata.',
            ],
            [
                'choice' => 'concrete class',
                'use_when' => 'One clear implementation solves the current behavior without replacement pressure.',
                'avoid_when' => 'Multiple callers already need different implementations or external services must be faked.',
                'laravel_example' => 'Keep InvoiceFormatter concrete until another formatter or caller contract exists.',
            ],
        ];
    }

    /**
     * Return the decision signals so learners can audit the recommendation.
     *
     * @return array<int, array{signal: string, value: string, impact: string}>
     */
    private function decisionMatrix(string $relationship, string $sharedBehavior, bool $needsMultipleImplementations, bool $hasSharedState): array
    {
        return [
            [
                'signal' => 'Class relationship',
                'value' => $this->relationshipLabel($relationship),
                'impact' => $relationship === 'unrelated'
                    ? 'Favors an interface because the classes should not inherit from one base class.'
                    : 'Favors inheritance only when the classes truly belong to one family.',
            ],
            [
                'signal' => 'Multiple implementations',
                'value' => $needsMultipleImplementations ? 'yes' : 'no',
                'impact' => $needsMultipleImplementations
                    ? 'Favors a contract that callers can depend on.'
                    : 'Weakens the case for adding an interface today.',
            ],
            [
                'signal' => 'Shared base behavior',
                'value' => $sharedBehavior !== '' ? $sharedBehavior : 'none declared',
                'impact' => $sharedBehavior !== ''
                    ? 'Favors an abstract class only when that behavior is stable and common.'
                    : 'Weakens the case for inheritance.',
            ],
            [
                'signal' => 'Shared state or helpers',
                'value' => $hasSharedState ? 'yes' : 'no',
                'impact' => $hasSharedState
                    ? 'Favors an abstract class for one object family.'
                    : 'Keeps the design closer to interface or concrete class.',
            ],
        ];
    }

    /**
     * Return a readable relationship label for API output.
     */
    private function relationshipLabel(string $relationship): string
    {
        return match ($relationship) {
            'unrelated' => 'unrelated classes',
            'same-family' => 'same object family',
            default => 'single stable class',
        };
    }

    /**
     * Pick the simplest useful abstraction for the scenario.
     */
    private function recommendation(string $relationship, string $sharedBehavior, bool $needsMultipleImplementations, bool $hasSharedState): string
    {
        if ($needsMultipleImplementations && $relationship === 'unrelated') {
            return 'interface';
        }

        if ($relationship === 'same-family' && ($hasSharedState || $sharedBehavior !== '')) {
            return 'abstract class';
        }

        return 'concrete class';
    }

    /**
     * Explain why the recommendation fits.
     */
    private function reason(string $recommendation, string $relationship, string $sharedBehavior, bool $needsMultipleImplementations, bool $hasSharedState): string
    {
        return match ($recommendation) {
            'interface' => 'Use an interface because unrelated classes can share a contract without inheriting the same base class.',
            'abstract class' => sprintf(
                'Use an abstract class because the classes are in the same family and share %s.',
                $hasSharedState ? 'state or base behavior' : "base behavior: {$sharedBehavior}"
            ),
            default => 'Use a concrete class first because the scenario does not yet prove that a separate abstraction earns its cost.',
        };
    }

    /**
     * Return a concise interview-ready answer.
     */
    private function interviewAnswer(string $recommendation): string
    {
        return match ($recommendation) {
            'interface' => 'An interface defines what a class must do. I use it when multiple unrelated implementations need the same contract, especially for replaceable dependencies.',
            'abstract class' => 'An abstract class can define shared base behavior or state while still forcing child classes to implement specific methods.',
            default => 'I would avoid adding an abstraction until there is a real second implementation, shared base behavior, or testing need.',
        };
    }

    /**
     * Return a small PHP code example for the recommendation.
     */
    private function codeExample(string $recommendation, string $scenario): string
    {
        $name = Str::studly($scenario);

        return match ($recommendation) {
            'interface' => <<<PHP
interface {$name}Contract
{
    public function handle(array \$payload): void;
}
PHP,
            'abstract class' => <<<PHP
abstract class Base{$name}
{
    public function __construct(protected string \$source)
    {
    }

    abstract public function handle(array \$payload): void;
}
PHP,
            default => <<<PHP
final class {$name}
{
    public function handle(array \$payload): void
    {
        // Keep this concrete until a real abstraction is needed.
    }
}
PHP,
        };
    }

    /**
     * Return tradeoffs to review before implementing the recommendation.
     *
     * @return array<int, string>
     */
    private function tradeoffs(string $recommendation): array
    {
        return match ($recommendation) {
            'interface' => [
                'A class can implement many interfaces.',
                'Interfaces are good for swappable dependencies but do not share implementation.',
                'Too many tiny interfaces can make the code harder to navigate.',
            ],
            'abstract class' => [
                'A class can extend only one parent class.',
                'Abstract classes can share implementation, helpers, and state.',
                'Inheritance can become rigid if the base class grows too much.',
            ],
            default => [
                'Concrete code is easier to read when there is only one behavior.',
                'You can extract an interface or abstract class later when the need becomes real.',
                'Avoid adding indirection only to make the code look more advanced.',
            ],
        };
    }

    /**
     * Return anti-pattern warnings for the selected abstraction.
     *
     * @return array<int, string>
     */
    private function antiPatterns(string $recommendation): array
    {
        return match ($recommendation) {
            'interface' => [
                'Creating an interface for every class before a second implementation or test fake exists.',
                'Copying every public method from a concrete class instead of naming the behavior the caller needs.',
                'Using an interface to hide unclear responsibilities instead of simplifying the class.',
            ],
            'abstract class' => [
                'Putting too much mutable state in the base class.',
                'Using inheritance only to share one small helper method.',
                'Forcing unrelated child classes into one family because the code happens to look similar today.',
            ],
            default => [
                'Keeping a concrete class after multiple callers need different implementations.',
                'Letting one concrete class grow many unrelated responsibilities.',
                'Avoiding abstraction even when tests require real external services.',
            ],
        };
    }

    /**
     * Return a testing strategy that matches the abstraction choice.
     *
     * @return array<int, string>
     */
    private function testingStrategy(string $recommendation, string $scenario): array
    {
        $name = Str::studly($scenario);

        return match ($recommendation) {
            'interface' => [
                "Write behavior tests against callers that type-hint {$name}Contract.",
                'Use a fake implementation to prove the caller does not depend on one concrete class.',
                'Avoid testing the interface itself; test the implementations and the caller behavior.',
            ],
            'abstract class' => [
                "Create a tiny test subclass of Base{$name} to verify shared base behavior.",
                'Test each concrete child for its required abstract method behavior.',
                'Avoid asserting protected helper internals unless they are observable through public behavior.',
            ],
            default => [
                "Write direct unit tests for {$name} before extracting any abstraction.",
                'Add an abstraction later only when a second implementation, shared base behavior, or testing need appears.',
                'Keep tests focused on output and side effects rather than future design guesses.',
            ],
        };
    }
}
