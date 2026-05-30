<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ContainerBindingPlanWorkbenchTest extends TestCase
{
    /**
     * The container binding workbench renders the dependency injection loop.
     */
    public function test_container_binding_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/container-binding-plan');

        $response
            ->assertOk()
            ->assertSee('Container Binding Plan Workbench')
            ->assertSee('POST /api/practice/container-binding-plan')
            ->assertSee('ContainerBindingPlanService')
            ->assertSee('Open DI refactor workbench')
            ->assertSee('Plan binding');
    }

    /**
     * The container binding API returns contract, binding, injection, and command details.
     */
    public function test_container_binding_plan_api_returns_plan(): void
    {
        $response = $this->postJson('/api/practice/container-binding-plan', [
            'contract_name' => 'Payment Gateway',
            'implementation_name' => 'Stripe Payment Gateway',
            'lifetime' => 'singleton',
            'injection_target' => 'Checkout Controller',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.contract', 'PaymentGateway')
            ->assertJsonPath('data.implementation', 'StripePaymentGateway')
            ->assertJsonPath('data.binding.method', 'singleton')
            ->assertJsonPath('data.injection.target', 'CheckoutController')
            ->assertJsonPath('data.steps.0', 'Create a contract that describes what the caller needs, not how the work is done.');
    }

    /**
     * Invalid container binding payloads return validation errors.
     */
    public function test_container_binding_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/container-binding-plan', [
            'contract_name' => '<bad>',
            'implementation_name' => 'x',
            'lifetime' => 'forever',
            'injection_target' => '',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['contract_name', 'implementation_name', 'lifetime', 'injection_target']);
    }
}
