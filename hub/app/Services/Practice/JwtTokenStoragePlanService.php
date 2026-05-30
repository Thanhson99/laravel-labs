<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class JwtTokenStoragePlanService
{
    /**
     * Build a JWT storage recommendation from browser, API, and security tradeoffs.
     *
     * @param  array{client_type: string, current_storage: string, token_lifetime: string, xss_risk: string, csrf_controls: string, refresh_token: string}  $input
     * @return array{recommendation: array<string, string>, decision_matrix: array<int, array{signal: string, value: string, impact: string}>, risk_score: array{score: int, level: string, reasons: array<int, string>}, tradeoffs: array<int, string>, controls: array<int, string>, review_checklist: array<int, array{area: string, question: string, pass_signal: string}>, implementation_snippets: array<string, string>, migration_plan: array{needed: bool, from: string, to: string, steps: array<int, string>}, tests: array<int, string>, interview_answer: string, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $recommendation = $this->recommendationFor($input);

        return [
            'recommendation' => $recommendation,
            'decision_matrix' => $this->decisionMatrixFor($input, $recommendation['storage']),
            'risk_score' => $this->riskScoreFor($input, $recommendation['storage']),
            'tradeoffs' => $this->tradeoffsFor($recommendation['storage']),
            'controls' => $this->controlsFor($input, $recommendation['storage']),
            'review_checklist' => $this->reviewChecklistFor($recommendation['storage']),
            'implementation_snippets' => $this->implementationSnippetsFor($recommendation['storage']),
            'migration_plan' => $this->migrationPlanFor($input['current_storage'], $recommendation['storage']),
            'tests' => $this->testsFor($recommendation['storage']),
            'interview_answer' => $this->interviewAnswerFor($recommendation['storage']),
            'commands' => [
                'php artisan route:list --path=jwt-token-storage-plan',
                'php artisan test --filter JwtTokenStoragePlan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return the input signals that explain why the recommendation was selected.
     *
     * @param  array{client_type: string, current_storage: string, token_lifetime: string, xss_risk: string, csrf_controls: string, refresh_token: string}  $input
     * @return array<int, array{signal: string, value: string, impact: string}>
     */
    private function decisionMatrixFor(array $input, string $recommendedStorage): array
    {
        return [
            [
                'signal' => 'Client type',
                'value' => $input['client_type'],
                'impact' => $this->clientTypeImpact($input['client_type']),
            ],
            [
                'signal' => 'Current storage',
                'value' => $input['current_storage'],
                'impact' => $input['current_storage'] === 'unknown'
                    ? 'Unknown current storage means the first task is discovery before migration.'
                    : "Current storage is compared with {$recommendedStorage} to decide whether migration is needed.",
            ],
            [
                'signal' => 'XSS risk',
                'value' => $input['xss_risk'],
                'impact' => $this->xssRiskImpact($input['xss_risk']),
            ],
            [
                'signal' => 'CSRF controls',
                'value' => $input['csrf_controls'],
                'impact' => $this->csrfControlImpact($input['csrf_controls']),
            ],
            [
                'signal' => 'Token lifetime',
                'value' => $input['token_lifetime'],
                'impact' => $this->tokenLifetimeImpact($input['token_lifetime']),
            ],
            [
                'signal' => 'Refresh token',
                'value' => $input['refresh_token'],
                'impact' => $input['refresh_token'] === 'yes'
                    ? 'Refresh tokens extend access, so rotation, reuse detection, and revocation become required.'
                    : 'Without refresh tokens, the UX may require re-authentication but the long-lived token risk is lower.',
            ],
        ];
    }

    /**
     * Explain the auth implication of the selected client type.
     */
    private function clientTypeImpact(string $clientType): string
    {
        return match ($clientType) {
            'same-domain-spa' => 'Same-domain browser apps can use cookie-based auth when CSRF and SameSite behavior are controlled.',
            'cross-domain-spa' => 'Cross-domain browser apps need extra care around credentials, SameSite, CORS, and CSRF expectations.',
            'mobile-app' => 'Mobile clients should use OS-backed secure storage rather than browser storage assumptions.',
            'third-party-api' => 'Third-party API clients usually need explicit scoped bearer tokens instead of browser cookie behavior.',
            default => 'Low-risk demos may use simpler storage, but the decision should not silently become production policy.',
        };
    }

    /**
     * Explain how XSS risk affects token storage.
     */
    private function xssRiskImpact(string $xssRisk): string
    {
        return match ($xssRisk) {
            'high' => 'High XSS risk strongly pushes away from JavaScript-readable token storage.',
            'medium' => 'Medium XSS risk requires output escaping, CSP review, and short token lifetime.',
            default => 'Low XSS risk lowers the score, but it does not remove the need for safe output and expiry.',
        };
    }

    /**
     * Explain how CSRF controls affect cookie-based auth.
     */
    private function csrfControlImpact(string $csrfControls): string
    {
        return match ($csrfControls) {
            'strong' => 'Strong CSRF controls make HttpOnly cookie flows much safer for browser clients.',
            'basic' => 'Basic CSRF controls may be insufficient for sensitive cookie-based auth until verified.',
            default => 'No CSRF controls means cookie-based state-changing requests are not ready for production.',
        };
    }

    /**
     * Explain how token lifetime changes the risk window.
     */
    private function tokenLifetimeImpact(string $tokenLifetime): string
    {
        return match ($tokenLifetime) {
            'days' => 'Day-long tokens create a large exposure window and should trigger stronger revocation controls.',
            'hours' => 'Hour-long tokens need clear refresh, logout, and revocation behavior.',
            default => 'Minute-long tokens reduce exposure but still need predictable refresh or re-authentication behavior.',
        };
    }

    /**
     * Return a simple risk score that makes the decision easy to review.
     *
     * @param  array{client_type: string, current_storage: string, token_lifetime: string, xss_risk: string, csrf_controls: string, refresh_token: string}  $input
     * @return array{score: int, level: string, reasons: array<int, string>}
     */
    private function riskScoreFor(array $input, string $storage): array
    {
        $score = 20;
        $reasons = [];

        if ($input['xss_risk'] === 'high') {
            $score += 30;
            $reasons[] = 'High XSS risk increases the chance that JavaScript-readable tokens are stolen.';
        } elseif ($input['xss_risk'] === 'medium') {
            $score += 15;
            $reasons[] = 'Medium XSS risk still requires output escaping, CSP review, and token minimization.';
        }

        if ($input['token_lifetime'] === 'days') {
            $score += 25;
            $reasons[] = 'Tokens that live for days create a larger damage window after leakage.';
        } elseif ($input['token_lifetime'] === 'hours') {
            $score += 10;
            $reasons[] = 'Hour-long tokens need clear expiry, refresh, and revocation behavior.';
        }

        if ($input['refresh_token'] === 'yes') {
            $score += 15;
            $reasons[] = 'Refresh tokens need rotation, reuse detection, and revocation because they extend access.';
        }

        if ($storage === 'localStorage-limited-demo') {
            $score += 25;
            $reasons[] = 'localStorage is JavaScript-readable, so it should stay limited to low-risk learning contexts.';
        }

        if ($storage === 'http-only-cookie' && $input['csrf_controls'] !== 'strong') {
            $score += 25;
            $reasons[] = 'Cookie-based flows need strong CSRF controls because browsers send cookies automatically.';
        }

        if ($storage === 'http-only-cookie' && $input['csrf_controls'] === 'strong') {
            $score -= 15;
            $reasons[] = 'Strong CSRF controls reduce the main risk introduced by cookie-based browser auth.';
        }

        $score = max(0, min(100, $score));

        return [
            'score' => $score,
            'level' => $this->riskLevelFor($score),
            'reasons' => $reasons === [] ? ['The selected context has no major token-storage warning signals.'] : $reasons,
        ];
    }

    /**
     * Convert a numeric score into a readable risk level.
     */
    private function riskLevelFor(int $score): string
    {
        if ($score >= 70) {
            return 'high';
        }

        if ($score >= 40) {
            return 'medium';
        }

        return 'controlled';
    }

    /**
     * Choose the recommended storage pattern for the submitted context.
     *
     * @param  array{client_type: string, current_storage: string, token_lifetime: string, xss_risk: string, csrf_controls: string, refresh_token: string}  $input
     * @return array{storage: string, label: string, reason: string, access_token: string, refresh_token: string}
     */
    private function recommendationFor(array $input): array
    {
        if ($input['client_type'] === 'mobile-app') {
            return [
                'storage' => 'secure-platform-storage',
                'label' => 'Use secure platform storage',
                'reason' => 'Mobile clients do not use browser cookies the same way. Store tokens in Keychain, Keystore, or an equivalent secure OS-backed store.',
                'access_token' => 'Short-lived bearer token.',
                'refresh_token' => $this->refreshTokenAdvice($input['refresh_token']),
            ];
        }

        if ($input['client_type'] === 'third-party-api') {
            return [
                'storage' => 'server-issued-bearer-token',
                'label' => 'Use explicit bearer tokens',
                'reason' => 'Third-party API clients usually cannot rely on browser cookies. Use scoped bearer tokens with expiry, rotation, and revocation.',
                'access_token' => 'Scoped bearer token with clear expiry.',
                'refresh_token' => 'Prefer client credentials or controlled rotation instead of browser refresh-token behavior.',
            ];
        }

        if ($input['client_type'] === 'low-risk-demo' && $input['token_lifetime'] === 'minutes' && $input['xss_risk'] === 'low') {
            return [
                'storage' => 'localStorage-limited-demo',
                'label' => 'localStorage only for low-risk demos',
                'reason' => 'The context is short-lived and low risk, so localStorage may be acceptable for learning or prototypes, but it should not become a default production choice.',
                'access_token' => 'Very short-lived access token.',
                'refresh_token' => 'Do not store long-lived refresh tokens in localStorage.',
            ];
        }

        if ($input['csrf_controls'] === 'strong') {
            return [
                'storage' => 'http-only-cookie',
                'label' => 'Use HttpOnly Secure SameSite cookies',
                'reason' => 'For browser apps with CSRF controls, an HttpOnly cookie reduces token theft from XSS because JavaScript cannot read the token directly.',
                'access_token' => 'Short-lived access token, ideally kept in memory or issued through a controlled session flow.',
                'refresh_token' => $this->refreshTokenAdvice($input['refresh_token']),
            ];
        }

        return [
            'storage' => 'memory-plus-short-lived-token',
            'label' => 'Avoid persistent browser storage',
            'reason' => 'Without strong CSRF controls, a cookie flow is incomplete. Avoid long-lived localStorage tokens and use short-lived in-memory access tokens until the auth design is hardened.',
            'access_token' => 'Short-lived in-memory access token.',
            'refresh_token' => 'Add CSRF controls before using cookie-based refresh tokens.',
        ];
    }

    /**
     * Return storage-specific tradeoffs learners should explain.
     *
     * @return array<int, string>
     */
    private function tradeoffsFor(string $storage): array
    {
        return match ($storage) {
            'http-only-cookie' => [
                'JavaScript cannot read the cookie, which reduces token exfiltration after XSS.',
                'The browser sends cookies automatically, so CSRF protection and SameSite configuration are required.',
                'Cookie domain, path, Secure, and proxy settings must match the deployment environment.',
            ],
            'localStorage-limited-demo' => [
                'Implementation is simple because JavaScript can attach the token to Authorization headers.',
                'Any successful XSS can read the token, so this should not hold long-lived or sensitive production tokens.',
                'Logout, rotation, and tab synchronization are easy to forget when token handling is manual.',
            ],
            'secure-platform-storage' => [
                'Mobile apps should use OS-backed secure storage instead of browser storage assumptions.',
                'Bearer token expiry, refresh, and revocation still need server-side enforcement.',
                'Device compromise and backup behavior should be part of the threat model.',
            ],
            'server-issued-bearer-token' => [
                'API clients need explicit bearer tokens rather than browser cookie behavior.',
                'Scopes, expiry, rotation, and audit logs matter more than where a browser would store the token.',
                'Never log raw bearer tokens or use token values as rate-limit identity keys.',
            ],
            default => [
                'In-memory tokens reduce persistence but disappear on refresh and require a refresh strategy.',
                'This is a bridge recommendation until CSRF, cookie, and refresh-token controls are designed clearly.',
                'Avoid turning a short-lived workaround into a long-lived production auth model.',
            ],
        };
    }

    /**
     * Return concrete security controls for the selected context.
     *
     * @param  array{client_type: string, current_storage: string, token_lifetime: string, xss_risk: string, csrf_controls: string, refresh_token: string}  $input
     * @return array<int, string>
     */
    private function controlsFor(array $input, string $storage): array
    {
        $controls = [
            'Keep access tokens short-lived and make expiry visible in tests.',
            'Sanitize output and keep Content Security Policy in the review checklist.',
            'Log token issue, refresh, revoke, and suspicious reuse events without logging raw token values.',
        ];

        if ($storage === 'http-only-cookie') {
            $controls[] = 'Set HttpOnly, Secure, SameSite, Path, Domain, and CSRF headers deliberately.';
            $controls[] = 'Test that state-changing requests fail without the expected CSRF signal.';
        }

        if ($input['refresh_token'] === 'yes') {
            $controls[] = 'Rotate refresh tokens and revoke the token family when reuse is detected.';
        }

        if ($input['xss_risk'] === 'high') {
            $controls[] = 'Treat localStorage as unsafe for sensitive tokens because the XSS risk is high.';
        }

        return $controls;
    }

    /**
     * Return review questions that should be asked before implementation.
     *
     * @return array<int, array{area: string, question: string, pass_signal: string}>
     */
    private function reviewChecklistFor(string $storage): array
    {
        $checklist = [
            [
                'area' => 'token lifetime',
                'question' => 'Is the access token short-lived enough for the account value and data sensitivity?',
                'pass_signal' => 'Expiry is documented and covered by an automated rejection test.',
            ],
            [
                'area' => 'revocation',
                'question' => 'Can the system revoke active access after logout, account suspension, or token reuse?',
                'pass_signal' => 'A revocation test proves old access stops working.',
            ],
            [
                'area' => 'logging',
                'question' => 'Do logs capture token lifecycle events without recording raw token values?',
                'pass_signal' => 'Logs include event type and actor ID, not bearer strings or cookie values.',
            ],
        ];

        if ($storage === 'http-only-cookie') {
            $checklist[] = [
                'area' => 'cookie flags',
                'question' => 'Are HttpOnly, Secure, SameSite, Path, and Domain set deliberately for the environment?',
                'pass_signal' => 'Browser devtools show the expected cookie flags in local and staging.',
            ];
            $checklist[] = [
                'area' => 'csrf',
                'question' => 'Do state-changing requests fail without the expected CSRF signal?',
                'pass_signal' => 'A feature test or browser smoke test proves CSRF enforcement.',
            ];
        }

        if ($storage === 'localStorage-limited-demo') {
            $checklist[] = [
                'area' => 'promotion guard',
                'question' => 'Is there an explicit warning that localStorage is not approved for sensitive production tokens?',
                'pass_signal' => 'The PR or lesson notes block promotion without a storage redesign.',
            ];
        }

        return $checklist;
    }

    /**
     * Return small implementation examples for the selected storage pattern.
     *
     * @return array<string, string>
     */
    private function implementationSnippetsFor(string $storage): array
    {
        return match ($storage) {
            'http-only-cookie' => [
                'cookie_header' => 'Set-Cookie: refresh_token=...; HttpOnly; Secure; SameSite=Lax; Path=/auth/refresh',
                'frontend_request' => "await fetch('/auth/refresh', {\n  method: 'POST',\n  credentials: 'include',\n  headers: { 'X-CSRF-TOKEN': csrfToken }\n});",
                'laravel_test' => "\$response = \$this->postJson('/auth/refresh');\n\$response->assertCookie('refresh_token');",
            ],
            'localStorage-limited-demo' => [
                'storage_write' => "localStorage.setItem('demo_access_token', token);",
                'frontend_request' => "fetch('/api/demo', {\n  headers: { Authorization: `Bearer \${localStorage.getItem('demo_access_token')}` }\n});",
                'review_warning' => 'Block production promotion until token storage is redesigned away from localStorage for sensitive access.',
            ],
            'secure-platform-storage' => [
                'mobile_storage' => 'Store tokens in Keychain, Keystore, or an equivalent OS-backed secure storage API.',
                'api_request' => 'Authorization: Bearer <short-lived-access-token>',
                'server_control' => 'Pair mobile storage with server-side expiry, rotation, revocation, and suspicious reuse detection.',
            ],
            'server-issued-bearer-token' => [
                'api_request' => 'Authorization: Bearer <scoped-api-token>',
                'server_control' => 'Store only a hash of the token server-side and show the raw token once.',
                'audit_event' => 'Record token_created, token_rotated, token_revoked, and token_used metadata without logging raw token values.',
            ],
            default => [
                'memory_storage' => 'let accessToken = response.access_token;',
                'frontend_request' => "fetch('/api/user', {\n  headers: { Authorization: `Bearer \${accessToken}` }\n});",
                'design_next_step' => 'Add CSRF controls and a deliberate refresh strategy before persisting browser auth state.',
            ],
        };
    }

    /**
     * Return migration steps from the currently used storage to the recommendation.
     *
     * @return array{needed: bool, from: string, to: string, steps: array<int, string>}
     */
    private function migrationPlanFor(string $currentStorage, string $recommendedStorage): array
    {
        $normalizedCurrent = $this->normalizeCurrentStorage($currentStorage);

        if ($normalizedCurrent === $recommendedStorage) {
            return [
                'needed' => false,
                'from' => $currentStorage,
                'to' => $recommendedStorage,
                'steps' => [
                    'Keep the current storage pattern, but still verify expiry, revocation, logging, and security headers.',
                ],
            ];
        }

        return [
            'needed' => true,
            'from' => $currentStorage,
            'to' => $recommendedStorage,
            'steps' => $this->migrationStepsFor($currentStorage, $recommendedStorage),
        ];
    }

    /**
     * Map user-facing storage labels onto internal recommendation keys.
     */
    private function normalizeCurrentStorage(string $currentStorage): string
    {
        return match ($currentStorage) {
            'localStorage' => 'localStorage-limited-demo',
            'http-only-cookie' => 'http-only-cookie',
            'memory' => 'memory-plus-short-lived-token',
            'bearer-token' => 'server-issued-bearer-token',
            'platform-storage' => 'secure-platform-storage',
            default => 'unknown',
        };
    }

    /**
     * Build practical migration steps for changing token storage.
     *
     * @return array<int, string>
     */
    private function migrationStepsFor(string $currentStorage, string $recommendedStorage): array
    {
        $steps = [
            'Document the current token lifetime, refresh behavior, logout behavior, and revocation gaps.',
            'Add tests for the current behavior before changing storage so regressions are visible.',
        ];

        if ($currentStorage === 'localStorage') {
            $steps[] = 'Stop storing refresh tokens in localStorage and introduce a server-controlled refresh path.';
            $steps[] = 'Clear old localStorage tokens during rollout after the new flow is accepted.';
        }

        if ($recommendedStorage === 'http-only-cookie') {
            $steps[] = 'Add HttpOnly, Secure, SameSite, Path, Domain, and CSRF controls before switching browser traffic.';
            $steps[] = 'Release behind a small browser cohort and verify cookies, CSRF, logout, and refresh in the Network tab.';
        } elseif ($recommendedStorage === 'secure-platform-storage') {
            $steps[] = 'Move token persistence into OS-backed secure storage and keep server-side expiry and revocation unchanged.';
        } elseif ($recommendedStorage === 'server-issued-bearer-token') {
            $steps[] = 'Issue scoped bearer tokens, store only token hashes server-side, and show raw tokens once.';
        } else {
            $steps[] = 'Keep access tokens in memory while the team finishes CSRF, refresh, and revocation design.';
        }

        $steps[] = 'Monitor token issue, refresh, revoke, and invalid-token events without logging raw token values.';

        return $steps;
    }

    /**
     * Return verification tests for the selected storage recommendation.
     *
     * @return array<int, string>
     */
    private function testsFor(string $storage): array
    {
        $tests = [
            'Expired access tokens are rejected.',
            'Logout revokes or invalidates the active token path.',
            'Raw tokens are not present in logs, rendered HTML, or JSON error payloads.',
        ];

        if ($storage === 'http-only-cookie') {
            $tests[] = 'Frontend JavaScript cannot read the auth cookie.';
            $tests[] = 'Cross-site state-changing requests fail without CSRF protection.';
        }

        if ($storage === 'localStorage-limited-demo') {
            $tests[] = 'A review warning blocks promotion to production while tokens are stored in localStorage.';
        }

        return $tests;
    }

    /**
     * Return refresh token guidance for browser and mobile contexts.
     */
    private function refreshTokenAdvice(string $refreshToken): string
    {
        if ($refreshToken !== 'yes') {
            return 'No refresh token selected; keep access tokens short and require re-authentication.';
        }

        return 'Use refresh-token rotation, reuse detection, revocation, and narrow cookie path when using browser cookies.';
    }

    /**
     * Return an interview-ready answer for the selected recommendation.
     */
    private function interviewAnswerFor(string $storage): string
    {
        if ($storage === 'localStorage-limited-demo') {
            return 'localStorage is simple, but it is exposed to XSS. I would only accept it for low-risk demos or very short-lived tokens, not as a default production JWT storage strategy.';
        }

        if ($storage === 'http-only-cookie') {
            return 'For browser apps, I usually prefer HttpOnly Secure SameSite cookies when CSRF controls are in place, because JavaScript cannot directly steal the token. I still need CSRF protection, expiry, rotation, and revocation.';
        }

        return 'The right JWT storage choice depends on the client and threat model. I compare XSS risk, CSRF risk, token lifetime, refresh strategy, revocation, and whether the client is a browser, mobile app, or external API consumer.';
    }
}
