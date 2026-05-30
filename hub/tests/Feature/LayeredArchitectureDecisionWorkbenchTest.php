<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class LayeredArchitectureDecisionWorkbenchTest extends TestCase
{
    /**
     * The layered architecture decision workbench renders the Clean Architecture P6 loop.
     */
    public function test_layered_architecture_decision_workbench_renders(): void
    {
        $response = $this->get('/workbench/layered-architecture-decision');

        $response
            ->assertOk()
            ->assertSee('Layered Architecture Decision Workbench')
            ->assertSee('POST /api/practice/layered-architecture-decision')
            ->assertSee('LayeredArchitectureDecisionService')
            ->assertSee('Simple CRUD screen')
            ->assertSee('Order checkout workflow')
            ->assertSee('External payment sync')
            ->assertSee('Decide layers');
    }

    /**
     * The layered architecture API keeps simple CRUD direct.
     */
    public function test_layered_architecture_api_keeps_simple_crud_direct(): void
    {
        $response = $this->postJson('/api/practice/layered-architecture-decision', [
            'feature_name' => 'Order Create',
            'feature_type' => 'crud',
            'business_rule_count' => 1,
            'integration_count' => 0,
            'persistence_complexity' => 'simple',
            'requires_async_work' => false,
            'requires_policy' => false,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.recommendation', 'keep simple')
            ->assertJsonPath('data.reason', 'The feature has low architecture pressure, so a Form Request, controller, model or query, and view/resource can stay readable without extra ceremony.')
            ->assertJsonPath('data.layer_plan.2.layer', 'Action or Service')
            ->assertJsonPath('data.layer_plan.2.recommendation', 'skip for now')
            ->assertJsonPath('data.implementation_steps.3', 'Do not add service or repository files until a real responsibility appears.')
            ->assertJsonPath('data.testing_strategy.0', 'Write a feature test for the OrderCreate HTTP behavior.')
            ->assertJsonPath('data.example_structure.1', 'app/Http/Controllers/OrderCreateController.php')
            ->assertJsonFragment(['feature_name' => 'Order Create']);
    }

    /**
     * The layered architecture API recommends explicit layers for high-pressure workflows.
     */
    public function test_layered_architecture_api_recommends_explicit_layers(): void
    {
        $response = $this->postJson('/api/practice/layered-architecture-decision', [
            'feature_name' => 'Checkout Submit',
            'feature_type' => 'workflow',
            'business_rule_count' => 4,
            'integration_count' => 1,
            'persistence_complexity' => 'complex',
            'requires_async_work' => true,
            'requires_policy' => true,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.recommendation', 'add explicit layers')
            ->assertJsonPath('data.score', 14)
            ->assertJsonPath('data.layer_plan.3.layer', 'Repository or Query Object')
            ->assertJsonPath('data.layer_plan.3.recommendation', 'use')
            ->assertJsonPath('data.layer_plan.5.recommendation', 'consider')
            ->assertJsonPath('data.example_structure.3', 'app/Actions/CheckoutSubmitAction.php')
            ->assertJsonPath('data.example_structure.5', 'app/Repositories/CheckoutSubmitRepository.php')
            ->assertJsonPath('data.testing_strategy.4', 'Use queue or event fakes to prove slow side effects are dispatched intentionally.')
            ->assertJsonPath('data.interview_answer', 'I use explicit layers when there are real boundaries: validation, authorization, business workflow, persistence complexity, async side effects, or integrations. Each layer must own a responsibility, not just forward data.');
    }

    /**
     * The layered architecture API validates invalid payloads.
     */
    public function test_layered_architecture_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/layered-architecture-decision', [
            'feature_name' => '<bad>',
            'feature_type' => 'unknown',
            'business_rule_count' => -1,
            'integration_count' => 99,
            'persistence_complexity' => 'mystery',
            'requires_async_work' => 'maybe',
            'requires_policy' => 'maybe',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'feature_name',
                'feature_type',
                'business_rule_count',
                'integration_count',
                'persistence_complexity',
                'requires_async_work',
                'requires_policy',
            ]);
    }
}
