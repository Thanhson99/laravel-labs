<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class DependencyInjectionRefactorService
{
    /**
     * Build a refactor plan that replaces manual object creation with dependency injection.
     *
     * @param  array{class_name: string, manual_dependency: string, dependency_role: string, contract_name: string, method_name: string, fake_name: string, binding_lifetime: string}  $input
     * @return array{class: string, dependency: string, contract: string, method: string, fake: string, risk_level: string, binding_method: string, lifetime_note: string, contract_example: string, implementation_stub: string, fake_example: string, container_test_example: string, before: string, after: string, binding: string, files: array<int, string>, refactor_map: array<int, array{file: string, change: string, reason: string}>, testing: array<string, string>, steps: array<int, string>, review_checklist: array<int, string>, anti_pattern_scan: array<int, string>, risk_controls: array<int, string>, overengineering_guardrails: array<int, string>, verification_commands: array<int, string>, commit_checklist: array<int, string>, warnings: array<int, string>}
     */
    public function plan(array $input): array
    {
        $class = Str::studly($input['class_name']);
        $dependency = Str::studly($input['manual_dependency']);
        $contract = Str::studly($input['contract_name']);
        $method = Str::camel($input['method_name']);
        $fake = Str::studly($input['fake_name']);
        $property = Str::camel($this->dependencyProperty($contract));
        $role = trim($input['dependency_role']);
        $bindingMethod = $this->bindingMethod($input['binding_lifetime']);
        $riskLevel = $this->riskLevel($role);

        return [
            'class' => $class,
            'dependency' => $dependency,
            'contract' => $contract,
            'method' => $method,
            'fake' => $fake,
            'risk_level' => $riskLevel,
            'binding_method' => $bindingMethod,
            'lifetime_note' => $this->lifetimeNote($bindingMethod),
            'contract_example' => $this->contractExample($contract, $method),
            'implementation_stub' => $this->implementationStub($dependency, $contract, $method),
            'fake_example' => $this->fakeExample($fake, $contract, $method),
            'container_test_example' => $this->containerTestExample($contract, $dependency, $fake),
            'before' => $this->beforeExample($class, $dependency, $method),
            'after' => $this->afterExample($class, $contract, $property, $method),
            'binding' => "\$this->app->{$bindingMethod}({$contract}::class, {$dependency}::class);",
            'files' => [
                "app/Contracts/{$contract}.php",
                "app/Services/{$dependency}.php",
                'app/Providers/AppServiceProvider.php',
                "tests/Fakes/{$fake}.php",
                "tests/Feature/{$class}Test.php",
            ],
            'refactor_map' => $this->refactorMap($class, $dependency, $contract, $fake, $bindingMethod, $role),
            'testing' => [
                'swap' => "\$this->app->instance({$contract}::class, new {$fake}());",
                'assertion' => "Assert that {$class} calls {$contract}::{$method}() for {$role}, not a concrete `new {$dependency}()` call.",
            ],
            'steps' => [
                "Extract {$contract} with a focused `{$method}` method for the {$role} behavior needed by {$class}.",
                "Make {$dependency} implement {$contract}::{$method}().",
                "Bind {$contract} to {$dependency} with `{$bindingMethod}` in a service provider.",
                "Inject {$contract} into {$class} through the constructor.",
                "Replace the manual `new {$dependency}()` call with the injected {$property} dependency.",
                "Swap {$contract} with {$fake} in a test to prove the dependency is replaceable.",
            ],
            'review_checklist' => [
                "No `new {$dependency}()` calls remain inside {$class}.",
                "{$contract}::{$method}() contains only behavior {$class} actually needs.",
                'The provider binding is covered by a feature test or container-resolution test.',
                "The fake proves the caller can run without the concrete {$dependency} implementation.",
            ],
            'anti_pattern_scan' => [
                "Search for `new {$dependency}(` inside controllers, services, jobs, and listeners.",
                "Search for constructor type-hints that depend on {$dependency} instead of {$contract}.",
                "Search for static helper calls that hide the same {$role} behavior.",
                'Check tests for assertions tied to implementation details instead of the contract behavior.',
            ],
            'risk_controls' => $this->riskControls($riskLevel, $contract, $fake, $role),
            'overengineering_guardrails' => $this->overengineeringGuardrails($class, $dependency, $contract, $role),
            'verification_commands' => [
                "php artisan test --filter {$class}Test",
                "php artisan test --filter {$contract}ContainerTest",
                'php artisan test --filter DependencyInjectionRefactorWorkbenchTest',
                'vendor\\bin\\pint --test',
            ],
            'commit_checklist' => [
                'The contract, implementation, binding, caller refactor, and fake are included in the same change.',
                'The test fails if the caller goes back to constructing the concrete dependency manually.',
                'The selected lifetime is justified by behavior, not chosen only for convenience.',
            ],
            'warnings' => [
                'Do not create the concrete implementation inside the controller or service after adding the constructor dependency.',
                'Keep the contract focused on the behavior this caller needs; avoid turning it into a large generic interface.',
                'Use `singleton` only when shared state and lifecycle are intentional. Most application services can start with `bind`.',
            ],
        ];
    }

    /**
     * Return a container-resolution test that proves binding and test swaps.
     */
    private function containerTestExample(string $contract, string $dependency, string $fake): string
    {
        $testMethodSuffix = $this->testMethodSuffix($contract);

        return <<<PHP
public function test_container_resolves_{$testMethodSuffix}(): void
{
    \$this->assertInstanceOf({$dependency}::class, \$this->app->make({$contract}::class));

    \$this->app->instance({$contract}::class, new {$fake}());

    \$this->assertInstanceOf({$fake}::class, \$this->app->make({$contract}::class));
}
PHP;
    }

    /**
     * Return a readable snake-case suffix for generated test methods.
     */
    private function testMethodSuffix(string $contract): string
    {
        return Str::of($contract)
            ->snake()
            ->toString();
    }

    /**
     * Return a file-by-file implementation map for the refactor.
     *
     * @return array<int, array{file: string, change: string, reason: string}>
     */
    private function refactorMap(string $class, string $dependency, string $contract, string $fake, string $bindingMethod, string $role): array
    {
        return [
            [
                'file' => "app/Contracts/{$contract}.php",
                'change' => "Create the {$contract} contract around the {$role} behavior.",
                'reason' => 'Callers can depend on behavior instead of one concrete implementation.',
            ],
            [
                'file' => "app/Services/{$dependency}.php",
                'change' => "Make {$dependency} implement {$contract}.",
                'reason' => 'The existing concrete behavior stays in place while the dependency boundary becomes explicit.',
            ],
            [
                'file' => 'app/Providers/AppServiceProvider.php',
                'change' => "Register the {$bindingMethod} binding from {$contract} to {$dependency}.",
                'reason' => 'The container can resolve the contract for constructor injection.',
            ],
            [
                'file' => "app/Services/{$class}.php",
                'change' => "Inject {$contract} and remove the manual `new {$dependency}()` call.",
                'reason' => 'The caller becomes replaceable in tests and no longer owns collaborator construction.',
            ],
            [
                'file' => "tests/Fakes/{$fake}.php",
                'change' => "Create {$fake} as a test double for {$contract}.",
                'reason' => 'Tests can prove the caller depends on the contract instead of the concrete integration.',
            ],
        ];
    }

    /**
     * Return a readable property name from the contract.
     */
    private function dependencyProperty(string $contract): string
    {
        return Str::of($contract)
            ->replaceEnd('Interface', '')
            ->replaceEnd('Contract', '')
            ->toString();
    }

    /**
     * Estimate refactor risk from words that usually imply side effects.
     */
    private function riskLevel(string $role): string
    {
        $role = Str::lower($role);

        foreach (['charge', 'payment', 'delete', 'send', 'sync', 'external', 'webhook'] as $keyword) {
            if (str_contains($role, $keyword)) {
                return 'high';
            }
        }

        return 'normal';
    }

    /**
     * Return extra safety controls for high-risk dependency refactors.
     *
     * @return array<int, string>
     */
    private function riskControls(string $riskLevel, string $contract, string $fake, string $role): array
    {
        if ($riskLevel !== 'high') {
            return [
                "Use {$fake} in tests to prove the caller only depends on {$contract}.",
                "Keep the refactor focused on {$role}; avoid mixing behavior changes into the same diff.",
            ];
        }

        return [
            "Block real side effects in tests by swapping {$contract} with {$fake}.",
            "Add at least one test that proves {$role} is not executed through the concrete integration during the refactor.",
            'Keep a rollback path: the binding can point back to the old implementation if production behavior changes.',
            'Review logs, queues, and external calls after deploy when the dependency touches payments, deletes, sync, or webhooks.',
        ];
    }

    /**
     * Return guardrails that keep Dependency Injection from becoming needless abstraction.
     *
     * @return array<int, string>
     */
    private function overengineeringGuardrails(string $class, string $dependency, string $contract, string $role): array
    {
        return [
            "Use {$contract} when {$role} may need a fake, vendor swap, or alternate implementation.",
            "Inject {$dependency} directly if {$class} only needs one stable application service and no replacement seam.",
            'Do not add a repository, manager, adapter, and contract at the same time unless each one has a clear responsibility.',
            'Prefer the smallest interface that expresses caller behavior over a broad interface copied from the concrete class.',
        ];
    }

    /**
     * Return the container binding method for the requested lifetime.
     */
    private function bindingMethod(string $lifetime): string
    {
        return $lifetime === 'singleton' ? 'singleton' : 'bind';
    }

    /**
     * Explain the impact of the selected binding lifetime.
     */
    private function lifetimeNote(string $bindingMethod): string
    {
        if ($bindingMethod === 'singleton') {
            return 'Laravel will reuse one resolved instance for the app lifecycle, so avoid hidden mutable state unless it is intentional.';
        }

        return 'Laravel will resolve a fresh instance when needed, which is usually a good default for application services.';
    }

    /**
     * Return the contract code sample.
     */
    private function contractExample(string $contract, string $method): string
    {
        return <<<PHP
interface {$contract}
{
    public function {$method}(array \$payload): string;
}
PHP;
    }

    /**
     * Return the implementation code stub.
     */
    private function implementationStub(string $dependency, string $contract, string $method): string
    {
        return <<<PHP
final class {$dependency} implements {$contract}
{
    public function {$method}(array \$payload): string
    {
        return 'result';
    }
}
PHP;
    }

    /**
     * Return a fake implementation code sample for tests.
     */
    private function fakeExample(string $fake, string $contract, string $method): string
    {
        return <<<PHP
final class {$fake} implements {$contract}
{
    public array \$calls = [];

    public function {$method}(array \$payload): string
    {
        \$this->calls[] = \$payload;

        return 'fake-result';
    }
}
PHP;
    }

    /**
     * Return the before-refactor code sample.
     */
    private function beforeExample(string $class, string $dependency, string $method): string
    {
        return <<<PHP
final class {$class}
{
    public function handle(): string
    {
        \$service = new {$dependency}();

        return \$service->{$method}([]);
    }
}
PHP;
    }

    /**
     * Return the after-refactor code sample.
     */
    private function afterExample(string $class, string $contract, string $property, string $method): string
    {
        return <<<PHP
final class {$class}
{
    public function __construct(private readonly {$contract} \${$property})
    {
    }

    public function handle(): string
    {
        return \$this->{$property}->{$method}([]);
    }
}
PHP;
    }
}
