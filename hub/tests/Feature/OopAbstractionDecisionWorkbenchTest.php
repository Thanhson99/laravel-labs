<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class OopAbstractionDecisionWorkbenchTest extends TestCase
{
    /**
     * The OOP abstraction decision workbench renders the abstract class versus interface loop.
     */
    public function test_oop_abstraction_decision_workbench_renders(): void
    {
        $response = $this->get('/workbench/oop-abstraction-decision');

        $response
            ->assertOk()
            ->assertSee('OOP Abstraction Decision Workbench')
            ->assertSee('POST /api/practice/oop-abstraction-decision')
            ->assertSee('OopAbstractionDecisionService')
            ->assertSee('Scenario preset')
            ->assertSee('File report family')
            ->assertSee('Invoice formatter')
            ->assertSee('Choose abstraction');
    }

    /**
     * The OOP abstraction decision API recommends an interface for unrelated swappable implementations.
     */
    public function test_oop_abstraction_decision_api_recommends_interface(): void
    {
        $response = $this->postJson('/api/practice/oop-abstraction-decision', [
            'scenario' => 'Payment Gateway',
            'relationship' => 'unrelated',
            'shared_behavior' => 'send a payment request',
            'needs_multiple_implementations' => true,
            'has_shared_state' => false,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.recommendation', 'interface')
            ->assertJsonPath('data.reason', 'Use an interface because unrelated classes can share a contract without inheriting the same base class.')
            ->assertJsonPath('data.interview_rubric.opening', 'Start by saying an interface is a contract for behavior, not shared implementation.')
            ->assertJsonPath('data.interview_rubric.example', 'Use PaymentGatewayContract when callers should work with any compatible implementation.')
            ->assertJsonPath('data.comparison_table.0.choice', 'interface')
            ->assertJsonPath('data.comparison_table.0.laravel_example', 'Bind PaymentGatewayContract to StripePaymentGateway in a service provider.')
            ->assertJsonPath('data.decision_matrix.0.signal', 'Class relationship')
            ->assertJsonPath('data.decision_matrix.0.value', 'unrelated classes')
            ->assertJsonPath('data.decision_matrix.1.impact', 'Favors a contract that callers can depend on.')
            ->assertJsonPath('data.implementation_plan.files.0', 'app/Contracts/PaymentGatewayContract.php')
            ->assertJsonPath('data.implementation_plan.steps.2', 'Bind PaymentGatewayContract to the concrete implementation in a service provider.')
            ->assertJsonPath('data.implementation_plan.review.2', 'A fake or second implementation proves the interface is earning its cost.')
            ->assertJsonPath('data.tradeoffs.0', 'A class can implement many interfaces.')
            ->assertJsonPath('data.anti_patterns.0', 'Creating an interface for every class before a second implementation or test fake exists.')
            ->assertJsonPath('data.testing_strategy.0', 'Write behavior tests against callers that type-hint PaymentGatewayContract.')
            ->assertJsonPath('data.checklist.0', 'Can unrelated classes implement this behavior?')
            ->assertJsonFragment(['scenario' => 'Payment Gateway']);
    }

    /**
     * The OOP abstraction decision API validates invalid payloads.
     */
    public function test_oop_abstraction_decision_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/oop-abstraction-decision', [
            'scenario' => '<bad>',
            'relationship' => 'unknown',
            'shared_behavior' => '<script>',
            'needs_multiple_implementations' => 'maybe',
            'has_shared_state' => 'maybe',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['scenario', 'relationship', 'shared_behavior', 'needs_multiple_implementations', 'has_shared_state']);
    }
}
