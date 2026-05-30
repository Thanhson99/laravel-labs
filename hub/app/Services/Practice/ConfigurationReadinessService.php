<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationReadinessService
{
    /**
     * Create a readiness plan for core Laravel configuration.
     */
    public function __construct(
        private readonly PracticeQualityGateService $qualityGate,
    ) {}

    /**
     * Build a read-only readiness report for app and auth configuration.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $checks = $this->checks();
        $misconfigurationControls = $this->misconfigurationControls();
        $deploymentSmokeMatrix = $this->deploymentSmokeMatrix();
        $baseline = $this->baselineFor($checks);

        return [
            'title' => 'Configuration Readiness Plan',
            'summary' => 'Read app, auth, and deployment configuration as code, then protect the expected runtime contract with focused tests and Security Misconfiguration smoke checks.',
            'checks' => $checks,
            'misconfiguration_controls' => $misconfigurationControls,
            'deployment_smoke_matrix' => $deploymentSmokeMatrix,
            'release_blockers' => $this->releaseBlockers($misconfigurationControls, $deploymentSmokeMatrix),
            'quality_gate' => $this->qualityGate->evaluate($baseline),
            'baseline' => $baseline,
            'commands' => [
                'php artisan test --filter ConfigurationReadinessTest',
                'php artisan test --filter PracticeQualityGate',
                'php artisan route:list --path=configuration-readiness',
                'vendor\\bin\\pint --test',
            ],
            'practice_targets' => [
                'hub/config/app.php',
                'hub/config/auth.php',
                'hub/config/session.php',
                'hub/config/cors.php',
                'hub/config/trustedproxy.php',
                'hub/app/Services/Practice/PracticeQualityGateService.php',
            ],
            'next_steps' => [
                'Assert the default locale, fallback locale, and app URL in a feature test.',
                'Assert the default guard, user provider, and password broker contract.',
                'Keep environment-specific values in config and read them through config(), not env() inside services.',
                'Add a smoke check that blocks release when APP_DEBUG, secrets, CORS, headers, storage, cookies, or trusted proxies are unsafe for production.',
            ],
        ];
    }

    /**
     * Return readiness checks for the current configuration snapshot.
     *
     * @return array<int, array{key: string, label: string, value: mixed, passed: bool, file: string}>
     */
    private function checks(): array
    {
        return [
            [
                'key' => 'app_name_present',
                'label' => 'Application name is configured.',
                'value' => config('app.name'),
                'passed' => filled(config('app.name')),
                'file' => 'hub/config/app.php',
            ],
            [
                'key' => 'app_url_present',
                'label' => 'Application URL is available for generated links.',
                'value' => config('app.url'),
                'passed' => filled(config('app.url')),
                'file' => 'hub/config/app.php',
            ],
            [
                'key' => 'locale_contract_present',
                'label' => 'Locale and fallback locale are defined.',
                'value' => config('app.locale').'/'.config('app.fallback_locale'),
                'passed' => filled(config('app.locale')) && filled(config('app.fallback_locale')),
                'file' => 'hub/config/app.php',
            ],
            [
                'key' => 'web_guard_uses_session',
                'label' => 'Web guard uses session authentication.',
                'value' => config('auth.guards.web.driver'),
                'passed' => config('auth.guards.web.driver') === 'session',
                'file' => 'hub/config/auth.php',
            ],
            [
                'key' => 'users_provider_uses_eloquent',
                'label' => 'Users provider resolves through Eloquent.',
                'value' => config('auth.providers.users.driver'),
                'passed' => config('auth.providers.users.driver') === 'eloquent',
                'file' => 'hub/config/auth.php',
            ],
            [
                'key' => 'password_broker_has_throttle',
                'label' => 'Password broker has a reset throttle.',
                'value' => config('auth.passwords.users.throttle'),
                'passed' => (int) config('auth.passwords.users.throttle') > 0,
                'file' => 'hub/config/auth.php',
            ],
        ];
    }

    /**
     * Return Security Misconfiguration controls that should be reviewed before deploy.
     *
     * @return array<int, array{area: string, risk: string, expected_control: string, evidence: string, owner: string}>
     */
    private function misconfigurationControls(): array
    {
        return [
            [
                'area' => 'Runtime environment',
                'risk' => 'APP_DEBUG, APP_ENV, verbose exceptions, or stale config cache makes production behave like local development.',
                'expected_control' => 'Production uses APP_DEBUG=false, APP_ENV=production, cached config, and redacted error output.',
                'evidence' => 'Feature test, deployment smoke check, and config cache command output.',
                'owner' => 'Release engineer',
            ],
            [
                'area' => 'Secret exposure',
                'risk' => '.env files, default credentials, debug toolbar, logs, or admin panels expose sensitive data.',
                'expected_control' => 'Secrets stay outside public web roots, logs redact sensitive values, and admin/debug tools are blocked in production.',
                'evidence' => 'HTTP smoke check for /.env, log redaction review, and admin/debug route inventory.',
                'owner' => 'Security reviewer',
            ],
            [
                'area' => 'Browser and edge boundary',
                'risk' => 'Broad CORS, missing security headers, weak cookie flags, missing HTTPS, or incorrect trusted proxies widen the attack surface.',
                'expected_control' => 'CORS uses explicit origins, headers are present, secure cookies are enabled, HTTPS is enforced, and proxy trust is scoped.',
                'evidence' => 'Response-header smoke check, session config assertion, and proxy boundary review.',
                'owner' => 'Platform engineer',
            ],
            [
                'area' => 'Storage exposure',
                'risk' => 'Public buckets, directory listing, wrong storage symlinks, or permissive file permissions expose private files.',
                'expected_control' => 'Private files use private disks, public files are intentional, directory listing is disabled, and permissions are reviewed.',
                'evidence' => 'Storage URL inventory, bucket policy review, and public-file smoke check.',
                'owner' => 'Application owner',
            ],
        ];
    }

    /**
     * Return fail-closed smoke checks for deployment configuration.
     *
     * @return array<int, array{check: string, unsafe_signal: string, fail_closed_action: string, verify_with: string}>
     */
    private function deploymentSmokeMatrix(): array
    {
        return [
            [
                'check' => 'Production debug guard',
                'unsafe_signal' => 'APP_DEBUG=true, local APP_ENV, or a stack trace appears in an HTTP response.',
                'fail_closed_action' => 'Block deploy and restore the last known-good environment file or secret set.',
                'verify_with' => 'GET a known failing route in production-like mode and assert no stack trace or secrets appear.',
            ],
            [
                'check' => 'Secret and admin exposure guard',
                'unsafe_signal' => '/.env, debug toolbar, default admin panel, or sensitive log path is publicly reachable.',
                'fail_closed_action' => 'Block deploy, remove exposure, rotate affected secrets, and record the incident note.',
                'verify_with' => 'HTTP smoke probe for forbidden paths plus route inventory for admin/debug endpoints.',
            ],
            [
                'check' => 'Browser boundary guard',
                'unsafe_signal' => 'CORS allows wildcard credentialed origins, required security headers are missing, or cookies are not secure.',
                'fail_closed_action' => 'Block deploy and apply the narrow origin, header, HTTPS, and cookie configuration.',
                'verify_with' => 'Response header check for CORS, HSTS, X-Frame-Options or CSP, Secure, HttpOnly, and SameSite expectations.',
            ],
            [
                'check' => 'Proxy and storage guard',
                'unsafe_signal' => 'Untrusted forwarded headers affect URL generation, or private files are reachable through public storage paths.',
                'fail_closed_action' => 'Block deploy until trusted proxy ranges and storage visibility match the environment contract.',
                'verify_with' => 'Proxy header simulation plus public/private storage URL probes.',
            ],
        ];
    }

    /**
     * Return release blockers derived from the Security Misconfiguration smoke matrix.
     *
     * @param  array<int, array{area: string}>  $controls
     * @param  array<int, array{check: string, unsafe_signal: string}>  $smokeMatrix
     * @return array<int, string>
     */
    private function releaseBlockers(array $controls, array $smokeMatrix): array
    {
        return [
            sprintf('No owner assigned for %d Security Misconfiguration control areas.', count($controls)),
            'Any production debug, secret exposure, broad CORS, missing header, weak cookie, proxy drift, or public storage signal blocks release.',
            sprintf('All %d deployment smoke checks must have captured evidence before promotion.', count($smokeMatrix)),
        ];
    }

    /**
     * Convert configuration checks into the shared quality-gate input shape.
     *
     * @param  array<int, array{passed: bool}>  $checks
     * @return array{tests: int, assertions: int, failures: int, pint: bool}
     */
    private function baselineFor(array $checks): array
    {
        $failures = collect($checks)
            ->reject(fn (array $check): bool => $check['passed'])
            ->count();

        return [
            'tests' => count($checks),
            'assertions' => count($checks),
            'failures' => $failures,
            'pint' => true,
        ];
    }
}
