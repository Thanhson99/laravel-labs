<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\DependencyInjectionRefactorService;
use PHPUnit\Framework\TestCase;

final class DependencyInjectionRefactorServiceTest extends TestCase
{
    /**
     * Refactor plans normalize names and expose the before/after architecture change.
     */
    public function test_refactor_plan_normalizes_dependency_names(): void
    {
        $plan = (new DependencyInjectionRefactorService)->plan([
            'class_name' => 'checkout service',
            'manual_dependency' => 'stripe payment gateway',
            'dependency_role' => 'charge customer payments',
            'contract_name' => 'payment gateway contract',
            'method_name' => 'chargeCustomer',
            'fake_name' => 'fake payment gateway',
            'binding_lifetime' => 'singleton',
        ]);

        $this->assertSame('CheckoutService', $plan['class']);
        $this->assertSame('StripePaymentGateway', $plan['dependency']);
        $this->assertSame('PaymentGatewayContract', $plan['contract']);
        $this->assertSame('chargeCustomer', $plan['method']);
        $this->assertSame('FakePaymentGateway', $plan['fake']);
        $this->assertSame('high', $plan['risk_level']);
        $this->assertSame('singleton', $plan['binding_method']);
        $this->assertStringContainsString('new StripePaymentGateway()', $plan['before']);
        $this->assertStringContainsString('->chargeCustomer([])', $plan['before']);
        $this->assertStringContainsString('function chargeCustomer(array $payload): string', $plan['contract_example']);
        $this->assertStringContainsString('implements PaymentGatewayContract', $plan['implementation_stub']);
        $this->assertStringContainsString('final class FakePaymentGateway implements PaymentGatewayContract', $plan['fake_example']);
        $this->assertStringContainsString('$this->calls[] = $payload;', $plan['fake_example']);
        $this->assertStringContainsString('test_container_resolves_payment_gateway_contract', $plan['container_test_example']);
        $this->assertStringContainsString('$this->app->make(PaymentGatewayContract::class)', $plan['container_test_example']);
        $this->assertStringContainsString('new FakePaymentGateway()', $plan['container_test_example']);
        $this->assertStringContainsString('private readonly PaymentGatewayContract $paymentGateway', $plan['after']);
        $this->assertStringContainsString('->chargeCustomer([])', $plan['after']);
        $this->assertSame('$this->app->singleton(PaymentGatewayContract::class, StripePaymentGateway::class);', $plan['binding']);
        $this->assertStringContainsString('reuse one resolved instance', $plan['lifetime_note']);
        $this->assertSame('app/Contracts/PaymentGatewayContract.php', $plan['files'][0]);
        $this->assertSame('app/Contracts/PaymentGatewayContract.php', $plan['refactor_map'][0]['file']);
        $this->assertSame('Create the PaymentGatewayContract contract around the charge customer payments behavior.', $plan['refactor_map'][0]['change']);
        $this->assertSame('The container can resolve the contract for constructor injection.', $plan['refactor_map'][2]['reason']);
        $this->assertStringContainsString('FakePaymentGateway', $plan['testing']['swap']);
        $this->assertSame('No `new StripePaymentGateway()` calls remain inside CheckoutService.', $plan['review_checklist'][0]);
        $this->assertSame('Search for `new StripePaymentGateway(` inside controllers, services, jobs, and listeners.', $plan['anti_pattern_scan'][0]);
        $this->assertSame('Block real side effects in tests by swapping PaymentGatewayContract with FakePaymentGateway.', $plan['risk_controls'][0]);
        $this->assertSame('Use PaymentGatewayContract when charge customer payments may need a fake, vendor swap, or alternate implementation.', $plan['overengineering_guardrails'][0]);
        $this->assertSame('php artisan test --filter CheckoutServiceTest', $plan['verification_commands'][0]);
        $this->assertSame('php artisan test --filter PaymentGatewayContractContainerTest', $plan['verification_commands'][1]);
        $this->assertSame('The selected lifetime is justified by behavior, not chosen only for convenience.', $plan['commit_checklist'][2]);
    }
}
