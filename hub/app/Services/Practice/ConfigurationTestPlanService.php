<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationTestPlanService
{
    /**
     * Create implementation-ready test plans for configuration contracts.
     */
    public function __construct(
        private readonly ConfigurationReadinessService $readiness,
    ) {}

    /**
     * Build PHPUnit-focused test guidance from the current readiness report.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $readiness = $this->readiness->build();
        $checks = collect($readiness['checks']);

        return [
            'title' => 'Configuration Test Plan',
            'summary' => 'Turn app and auth configuration expectations into focused feature tests before changing runtime defaults.',
            'target_test' => 'hub/tests/Feature/ConfigurationReadinessTest.php',
            'source_files' => $readiness['practice_targets'],
            'test_groups' => [
                $this->groupFor('Application runtime contract', $checks->where('file', 'hub/config/app.php')->values()->all()),
                $this->groupFor('Authentication contract', $checks->where('file', 'hub/config/auth.php')->values()->all()),
                $this->securityMisconfigurationGroup($readiness),
                $this->groupFor('Quality-gate contract', [
                    [
                        'key' => 'quality_gate_ready_contract',
                        'label' => 'Quality gate marks the current configuration baseline as ready.',
                        'value' => $readiness['quality_gate']['status'],
                        'passed' => $readiness['quality_gate']['passed'],
                        'file' => 'hub/app/Services/Practice/PracticeQualityGateService.php',
                    ],
                ]),
            ],
            'snippet' => $this->snippet(),
            'commands' => [
                'php artisan test --filter ConfigurationReadinessTest',
                'php artisan test --filter ConfigurationTestPlanTest',
                'php artisan route:list --path=configuration-readiness',
                'vendor\\bin\\pint --test',
            ],
            'quality_gate' => $readiness['quality_gate'],
            'baseline' => $readiness['baseline'],
        ];
    }

    /**
     * Build one grouped test-plan section.
     *
     * @param  array<int, array<string, mixed>>  $checks
     * @return array<string, mixed>
     */
    private function groupFor(string $name, array $checks): array
    {
        return [
            'name' => $name,
            'checks' => $checks,
            'assertions' => collect($checks)
                ->map(fn (array $check): string => $this->assertionFor($check))
                ->values()
                ->all(),
        ];
    }

    /**
     * Convert a readiness check into a concise PHPUnit assertion idea.
     *
     * @param  array<string, mixed>  $check
     */
    private function assertionFor(array $check): string
    {
        return match ($check['key']) {
            'app_name_present' => "expect(config('app.name'))->not->toBeEmpty();",
            'app_url_present' => "expect(config('app.url'))->not->toBeEmpty();",
            'locale_contract_present' => "expect(config('app.locale'))->toBe('en');",
            'web_guard_uses_session' => "expect(config('auth.guards.web.driver'))->toBe('session');",
            'users_provider_uses_eloquent' => "expect(config('auth.providers.users.driver'))->toBe('eloquent');",
            'password_broker_has_throttle' => "expect(config('auth.passwords.users.throttle'))->toBeGreaterThan(0);",
            'quality_gate_ready_contract' => "expect((new PracticeQualityGateService)->evaluate(\$baseline)['status'])->toBe('ready');",
            default => sprintf('expect(%s)->toBeTrue();', var_export((string) $check['key'], true)),
        };
    }

    /**
     * Build test ideas for Security Misconfiguration controls and smoke checks.
     *
     * @param  array<string, mixed>  $readiness
     * @return array{name: string, checks: array<int, array<string, mixed>>, assertions: array<int, string>}
     */
    private function securityMisconfigurationGroup(array $readiness): array
    {
        $controls = collect($readiness['misconfiguration_controls'])
            ->map(fn (array $control): array => [
                'key' => str($control['area'])->slug('_')->toString(),
                'label' => $control['expected_control'],
                'value' => $control['owner'],
                'passed' => true,
                'file' => 'hub/app/Services/Practice/ConfigurationReadinessService.php',
            ])
            ->values();

        $smokeChecks = collect($readiness['deployment_smoke_matrix'])
            ->map(fn (array $smoke): array => [
                'key' => str($smoke['check'])->slug('_')->toString(),
                'label' => $smoke['fail_closed_action'],
                'value' => $smoke['unsafe_signal'],
                'passed' => true,
                'file' => 'deployment smoke checks',
            ])
            ->values();

        $checks = $controls
            ->merge($smokeChecks)
            ->all();

        return [
            'name' => 'Security Misconfiguration contract',
            'checks' => $checks,
            'assertions' => [
                "expect(config('app.debug'))->not->toBeTrue();",
                'assert response smoke checks reject exposed .env, debug toolbar, broad CORS, missing headers, weak cookies, proxy drift, and public private-storage paths.',
                'assert every configuration release blocker has an owner, fail-closed action, and verification evidence.',
            ],
        ];
    }

    /**
     * Return a starter test snippet learners can adapt.
     */
    private function snippet(): string
    {
        return <<<'PHP'
public function test_app_and_auth_configuration_contract_is_ready(): void
{
    $this->assertNotEmpty(config('app.name'));
    $this->assertNotEmpty(config('app.url'));
    $this->assertSame('en', config('app.locale'));
    $this->assertSame('session', config('auth.guards.web.driver'));
    $this->assertSame('eloquent', config('auth.providers.users.driver'));
    $this->assertGreaterThan(0, config('auth.passwords.users.throttle'));
}
PHP;
    }
}
