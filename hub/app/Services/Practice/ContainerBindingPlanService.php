<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class ContainerBindingPlanService
{
    /**
     * Build a service-container binding plan for dependency inversion practice.
     *
     * @param  array{contract_name: string, implementation_name: string, lifetime: string, injection_target: string}  $input
     * @return array{contract: string, implementation: string, binding: array<string, string>, injection: array<string, string>, steps: array<int, string>, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $contract = Str::studly($input['contract_name']);
        $implementation = Str::studly($input['implementation_name']);
        $target = Str::studly($input['injection_target']);

        return [
            'contract' => $contract,
            'implementation' => $implementation,
            'binding' => [
                'provider' => 'AppServiceProvider',
                'method' => $input['lifetime'] === 'singleton' ? 'singleton' : 'bind',
                'example' => "\$this->app->{$this->bindingMethod($input['lifetime'])}({$contract}::class, {$implementation}::class);",
            ],
            'injection' => [
                'target' => $target,
                'example' => "public function __construct(private readonly {$contract} \$service) {}",
            ],
            'steps' => [
                'Create a contract that describes what the caller needs, not how the work is done.',
                'Create one implementation that satisfies the contract.',
                'Bind the contract to the implementation in a service provider.',
                'Inject the contract into the controller, service, listener, or job.',
                'Test the caller by swapping the implementation with a fake or stub.',
            ],
            'commands' => [
                'php artisan make:provider AppServiceProvider',
                'php artisan test --filter ContainerBindingPlanWorkbenchTest',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return the container binding method for the requested lifetime.
     */
    private function bindingMethod(string $lifetime): string
    {
        return $lifetime === 'singleton' ? 'singleton' : 'bind';
    }
}
