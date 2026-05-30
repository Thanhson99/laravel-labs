<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class JwtRevocationPlanService
{
    /**
     * Build a practical JWT revocation plan for API and database design practice.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array{strategy: array<string, string>, architecture_summary: array{decision: string, primary_tradeoff: string, when_not_to_use: string, next_design_decision: string}, threat_model: array<int, array{asset: string, attacker_scenario: string, trust_boundary: string, control: string}>, incident_playbook: array<int, array{phase: string, trigger: string, actions: array<int, string>, evidence_to_capture: array<int, string>}>, decision_matrix: array<int, array{signal: string, value: string, decision_impact: string}>, risk_score: array{score: int, level: string, reasons: array<int, string>}, database_schema: array<int, string>, retention_plan: array{storage: string, indexes: array<int, string>, prune_policy: string, audit_policy: string, capacity_note: string}, api_endpoints: array<int, array{method: string, path: string, purpose: string}>, middleware_flow: array<int, string>, revocation_steps: array<int, string>, rollout_plan: array<int, array{phase: string, goal: string, actions: array<int, string>, rollback_signal: string}>, observability_plan: array{metrics: array<int, string>, log_events: array<int, string>, alerts: array<int, string>}, failure_mode_plan: array<int, array{failure: string, default_policy: string, mitigation: array<int, string>, recovery_check: string}>, test_matrix: array<int, array{category: string, case: string, expected_result: string, automation_hint: string}>, review_checklist: array<int, array{area: string, question: string, pass_signal: string}>, risk_notes: array<int, string>, implementation_snippets: array<string, string>, tests: array<int, string>, interview_answer: string, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $strategy = $this->strategyFor($input);

        return [
            'strategy' => $strategy,
            'architecture_summary' => $this->architectureSummaryFor($input, $strategy),
            'threat_model' => $this->threatModelFor($input, $strategy['model']),
            'incident_playbook' => $this->incidentPlaybookFor($input, $strategy['model']),
            'decision_matrix' => $this->decisionMatrixFor($input, $strategy['model']),
            'risk_score' => $this->riskScoreFor($input, $strategy['model']),
            'database_schema' => $this->databaseSchemaFor($input),
            'retention_plan' => $this->retentionPlanFor($input, $strategy['model']),
            'api_endpoints' => $this->apiEndpointsFor($input),
            'middleware_flow' => $this->middlewareFlowFor($input),
            'revocation_steps' => $this->revocationStepsFor($input),
            'rollout_plan' => $this->rolloutPlanFor($input, $strategy['model']),
            'observability_plan' => $this->observabilityPlanFor($input, $strategy['model']),
            'failure_mode_plan' => $this->failureModePlanFor($input, $strategy['model']),
            'test_matrix' => $this->testMatrixFor($input, $strategy['model']),
            'review_checklist' => $this->reviewChecklistFor($input, $strategy['model']),
            'risk_notes' => $this->riskNotesFor($input),
            'implementation_snippets' => $this->implementationSnippetsFor($input),
            'tests' => $this->testsFor($input),
            'interview_answer' => $this->interviewAnswerFor($strategy['model']),
            'commands' => [
                'php artisan route:list --path=jwt-revocation-plan',
                'php artisan test --filter JwtRevocationPlan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return incident-response steps for suspected JWT compromise.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array<int, array{phase: string, trigger: string, actions: array<int, string>, evidence_to_capture: array<int, string>}>
     */
    private function incidentPlaybookFor(array $input, string $selectedModel): array
    {
        $playbook = [
            [
                'phase' => 'triage',
                'trigger' => 'Suspicious token reuse, leaked bearer value, unusual 401 spike, or account takeover report.',
                'actions' => [
                    'Identify affected user IDs, device IDs, token family hashes, jti hashes, route groups, and time window.',
                    'Classify route sensitivity so admin, billing, and account-security paths receive stricter containment.',
                    'Confirm whether the signal is token theft, refresh reuse, stale authorization, or infrastructure failure.',
                ],
                'evidence_to_capture' => [
                    'correlation IDs',
                    'token_rejected and refresh_reuse_detected events',
                    'user_id, device_id, jti hash, and token family hash',
                ],
            ],
            [
                'phase' => 'contain',
                'trigger' => 'Confirmed compromise or high-confidence token replay.',
                'actions' => [
                    "Apply {$selectedModel} revocation for affected access tokens.",
                    'Revoke refresh-token families and force re-authentication for affected devices.',
                    $input['immediate_logout'] === 'yes'
                        ? 'Run logout-all for affected accounts and high-risk cohorts.'
                        : 'Shorten active access-token lifetime and block refresh for affected families.',
                ],
                'evidence_to_capture' => [
                    'revocation records inserted',
                    'logout-all request actor and reason',
                    'post-containment rejected token count',
                ],
            ],
            [
                'phase' => 'recover',
                'trigger' => 'Containment has stopped new token abuse.',
                'actions' => [
                    'Re-issue tokens only after password reset, step-up verification, or device trust review when needed.',
                    'Verify revoked jtis, stale versions, and token families remain blocked until expected expiry.',
                    'Review token storage, CSRF/XSS controls, and refresh-token rotation settings before closing the incident.',
                ],
                'evidence_to_capture' => [
                    'successful clean login after recovery',
                    'blocked replay attempts after containment',
                    'root-cause notes and follow-up owner',
                ],
            ],
        ];

        if ($input['client_type'] === 'browser-spa') {
            $playbook[2]['actions'][] = 'Inspect browser-facing XSS, CSRF, cookie, and CORS controls before trusting the recovered session.';
        }

        return $playbook;
    }

    /**
     * Return a compact threat model for the JWT revocation design.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array<int, array{asset: string, attacker_scenario: string, trust_boundary: string, control: string}>
     */
    private function threatModelFor(array $input, string $selectedModel): array
    {
        $model = [
            [
                'asset' => 'access token',
                'attacker_scenario' => 'A leaked JWT is replayed before its exp claim.',
                'trust_boundary' => "Protected API route plus {$input['revocation_store']} revocation lookup.",
                'control' => $selectedModel === 'short-lived-access-token'
                    ? 'Keep access tokens very short-lived and revoke refresh credentials.'
                    : "Check {$selectedModel} state before authorization and log token_rejected events.",
            ],
            [
                'asset' => 'refresh token family',
                'attacker_scenario' => 'An old refresh token is reused after rotation.',
                'trust_boundary' => 'Refresh endpoint, token-family store, and device/session metadata.',
                'control' => $input['refresh_rotation'] === 'yes'
                    ? 'Detect reuse, revoke the token family, and require re-authentication.'
                    : 'Avoid long-lived refresh credentials or add rotation before production use.',
            ],
            [
                'asset' => 'user authorization state',
                'attacker_scenario' => 'A user keeps using an old token after role downgrade, password change, or account lock.',
                'trust_boundary' => 'Token claims versus current user, session, and policy state.',
                'control' => $selectedModel === 'token-version'
                    ? 'Compare token version with current security version on every protected request.'
                    : 'Revoke active tokens or refresh families when security-sensitive account state changes.',
            ],
        ];

        if ($input['client_type'] === 'browser-spa') {
            $model[] = [
                'asset' => 'browser session',
                'attacker_scenario' => 'XSS or CSRF pressure turns token lifecycle bugs into account takeover risk.',
                'trust_boundary' => 'Browser storage, cookie policy, CSRF controls, and API auth middleware.',
                'control' => 'Pair revocation with short access-token lifetime, safe token storage, CSRF protection for cookie flows, and XSS prevention.',
            ];
        }

        return $model;
    }

    /**
     * Summarize the architecture choice before listing implementation details.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @param  array<string, string>  $strategy
     * @return array{decision: string, primary_tradeoff: string, when_not_to_use: string, next_design_decision: string}
     */
    private function architectureSummaryFor(array $input, array $strategy): array
    {
        return [
            'decision' => "{$strategy['label']} for {$input['client_type']} with {$input['token_lifetime']} access tokens.",
            'primary_tradeoff' => $this->primaryTradeoffFor($strategy['model']),
            'when_not_to_use' => $this->avoidanceGuidanceFor($strategy['model']),
            'next_design_decision' => $input['refresh_rotation'] === 'yes'
                ? 'Define refresh-token family storage, reuse detection, and account recovery behavior.'
                : 'Decide whether the UX can tolerate re-authentication without refresh-token rotation.',
        ];
    }

    /**
     * Explain the main tradeoff behind a revocation model.
     */
    private function primaryTradeoffFor(string $model): string
    {
        return match ($model) {
            'token-version' => 'Simple user-wide invalidation, but every protected request needs a reliable user or session version lookup.',
            'refresh-rotation' => 'Strong control over future access, but access tokens remain valid until expiry unless another stateful check is added.',
            'short-lived-access-token' => 'Lowest runtime lookup cost, but immediate logout is weaker because the system waits for expiry.',
            default => 'Precise per-token revocation, but every protected request pays for a revocation lookup and store availability matters.',
        };
    }

    /**
     * Explain when the selected revocation model is a poor fit.
     */
    private function avoidanceGuidanceFor(string $model): string
    {
        return match ($model) {
            'token-version' => 'Avoid relying only on token version when a single device or one token must be revoked without logging out other sessions.',
            'refresh-rotation' => 'Avoid relying only on refresh rotation when high-risk access tokens live for hours or days.',
            'short-lived-access-token' => 'Avoid this as the only control when the product requires immediate logout, account lockout, or lost-device response.',
            default => 'Avoid denylist-only designs when the revocation store cannot meet protected-route latency and availability requirements.',
        };
    }

    /**
     * Return retention, indexing, and pruning guidance for revocation data.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array{storage: string, indexes: array<int, string>, prune_policy: string, audit_policy: string, capacity_note: string}
     */
    private function retentionPlanFor(array $input, string $selectedModel): array
    {
        $indexes = [
            'primary or unique index on jti or token hash for constant-time revocation lookup',
            'index on user_id and revoked_at for account security review',
            'index on expires_at for pruning expired revocation records',
        ];

        if ($selectedModel === 'token-version') {
            $indexes[] = 'index user security_version lookups by user_id or session_id';
        }

        if ($input['refresh_rotation'] === 'yes') {
            $indexes[] = 'index refresh_token_families by family_id hash, user_id, device_id, and revoked_at';
        }

        return [
            'storage' => $input['revocation_store'],
            'indexes' => $indexes,
            'prune_policy' => $input['revocation_store'] === 'redis'
                ? 'Use Redis TTL equal to token exp plus clock-skew buffer, and keep database audit records only when compliance or incident review requires it.'
                : 'Run a scheduled prune job for revoked records whose expires_at is older than the clock-skew buffer.',
            'audit_policy' => 'Keep audit metadata long enough for incident review, but store jti hashes and event metadata instead of raw token values.',
            'capacity_note' => $this->capacityNoteFor($input['token_lifetime'], $input['revocation_store']),
        ];
    }

    /**
     * Explain capacity risk for revocation storage.
     */
    private function capacityNoteFor(string $tokenLifetime, string $revocationStore): string
    {
        if ($tokenLifetime === 'days') {
            return "Day-long tokens keep revoked entries around longer, so {$revocationStore} capacity must be sized for peak logout, incident, and device-loss bursts.";
        }

        if ($revocationStore === 'cache') {
            return 'Cache-backed revocation needs eviction monitoring because capacity pressure can remove entries before token expiry.';
        }

        return 'Shorter access-token lifetime keeps revocation storage bounded when pruning runs reliably.';
    }

    /**
     * Return a structured verification matrix for feature, security, and operations tests.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array<int, array{category: string, case: string, expected_result: string, automation_hint: string}>
     */
    private function testMatrixFor(array $input, string $selectedModel): array
    {
        $matrix = [
            [
                'category' => 'feature',
                'case' => 'valid active token reaches a protected route',
                'expected_result' => 'Request succeeds and authorization policy or scope still runs.',
                'automation_hint' => 'Feature test a protected JSON route with a signed token that is not expired or revoked.',
            ],
            [
                'category' => 'security',
                'case' => "revoked token is blocked by {$selectedModel}",
                'expected_result' => 'Request returns 401 even when signature and expiry are valid.',
                'automation_hint' => "Seed {$input['revocation_store']} revocation state, call the route, and assert unauthorized response plus token_rejected event.",
            ],
            [
                'category' => 'operations',
                'case' => 'expired revocation records are pruned',
                'expected_result' => 'Records remain until token expiry and are removed after the configured retention buffer.',
                'automation_hint' => 'Freeze time, create revoked records around expires_at, run prune command, and assert only expired records are removed.',
            ],
            [
                'category' => 'privacy',
                'case' => 'raw tokens are absent from logs and error responses',
                'expected_result' => 'Logs contain event metadata and jti hash, not bearer strings or refresh token values.',
                'automation_hint' => 'Trigger revoke and reject paths, capture logs, and assert token substrings are not present.',
            ],
        ];

        if ($input['refresh_rotation'] === 'yes') {
            $matrix[] = [
                'category' => 'security',
                'case' => 'old refresh token reuse revokes the token family',
                'expected_result' => 'The reused refresh token is rejected and the token family cannot mint new access tokens.',
                'automation_hint' => 'Rotate a refresh token, replay the old value, then assert family revoked and refresh_reuse_detected event emitted.',
            ];
        }

        if ($selectedModel === 'token-version') {
            $matrix[] = [
                'category' => 'authorization',
                'case' => 'stale token version is rejected after role or password change',
                'expected_result' => 'Old token returns 401 while a newly issued token uses the updated security version.',
                'automation_hint' => 'Issue token, bump user security version, call protected route, and assert stale-version rejection.',
            ];
        }

        return $matrix;
    }

    /**
     * Return failure handling guidance for revocation dependencies.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array<int, array{failure: string, default_policy: string, mitigation: array<int, string>, recovery_check: string}>
     */
    private function failureModePlanFor(array $input, string $selectedModel): array
    {
        $plan = [
            [
                'failure' => 'revocation store unavailable',
                'default_policy' => $input['immediate_logout'] === 'yes'
                    ? 'Fail closed for high-risk or privileged routes; fail with a short retry path for lower-risk routes.'
                    : 'Fail open only for low-risk read routes when access tokens are short-lived and the outage is observable.',
                'mitigation' => [
                    'Set a tight timeout for revocation lookup so protected routes do not hang.',
                    'Use route risk tiers so admin, billing, and account-security routes can fail closed.',
                    'Alert on lookup errors and record correlation IDs for affected requests.',
                ],
                'recovery_check' => 'Replay audit-only checks after the store recovers and confirm no revoked token was accepted on protected routes.',
            ],
            [
                'failure' => 'stale or evicted revocation entry',
                'default_policy' => 'Treat unexpected cache miss as a design risk when the token should still be inside its expiry window.',
                'mitigation' => [
                    'Store revoked entries with TTL equal to token expiry plus a small clock-skew buffer.',
                    'Use database audit records when cache eviction cannot be ruled out.',
                    'Monitor revoked-token acceptance and cache eviction counters together.',
                ],
                'recovery_check' => 'Sample revoked jtis and verify they remain blocked until their original expires_at value.',
            ],
            [
                'failure' => 'refresh-token reuse spike',
                'default_policy' => 'Revoke the affected refresh-token family and require re-authentication for the device or account.',
                'mitigation' => [
                    'Throttle refresh attempts for the affected user, device, IP range, or client type.',
                    'Notify the account owner or trigger step-up verification when the account value is high.',
                    'Preserve forensic logs without storing raw refresh tokens.',
                ],
                'recovery_check' => 'Verify that old refresh tokens cannot mint new access tokens after family revocation.',
            ],
        ];

        if ($selectedModel === 'token-version') {
            $plan[] = [
                'failure' => 'token version drift',
                'default_policy' => 'Reject tokens whose version cannot be matched confidently to the current user or session security version.',
                'mitigation' => [
                    'Use transactional updates when bumping security version after password, role, or account-status changes.',
                    'Emit token_version_bumped events with actor and reason metadata.',
                    'Add a support path for users logged out after legitimate security-version changes.',
                ],
                'recovery_check' => 'Confirm stale-version rejections match the intended password, role, or account-status event.',
            ];
        }

        return $plan;
    }

    /**
     * Return metrics, log events, and alerts for operating JWT revocation.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array{metrics: array<int, string>, log_events: array<int, string>, alerts: array<int, string>}
     */
    private function observabilityPlanFor(array $input, string $selectedModel): array
    {
        $metrics = [
            'jwt_issued_total by client_type and grant_type',
            'jwt_revoked_total by reason and revocation_model',
            'jwt_rejected_total by reason: expired, revoked, invalid_signature, stale_version',
            "revocation_store_latency_ms for {$input['revocation_store']} lookups",
            'auth_refresh_success_rate and auth_refresh_reuse_detected_total',
        ];

        $logEvents = [
            'token_issued with user_id, device_id, jti hash, exp, scopes, and client_type',
            'token_revoked with user_id, device_id, jti hash, reason, actor_id, and expires_at',
            'token_rejected with reason, route name, guard, user_id when known, and request correlation ID',
            'refresh_reuse_detected with token family ID hash and affected user_id',
        ];

        $alerts = [
            'Revocation store lookup latency or error rate exceeds the protected-route budget.',
            'Revoked-token acceptance is detected in audit-only or enforcement mode.',
            'Refresh-token reuse spikes for one user, device family, IP range, or client type.',
            'Logout success drops or 401 rate rises sharply after enabling enforcement.',
        ];

        if ($selectedModel === 'token-version') {
            $metrics[] = 'jwt_stale_version_rejected_total by user_status and route group';
            $logEvents[] = 'token_version_bumped with user_id, old_version, new_version, reason, and actor_id';
        }

        if ($input['token_lifetime'] === 'days') {
            $alerts[] = 'Long-lived token rejection increases without matching logout or password-change events.';
        }

        return [
            'metrics' => $metrics,
            'log_events' => $logEvents,
            'alerts' => $alerts,
        ];
    }

    /**
     * Return a rollout plan for adding revocation to an existing JWT system.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array<int, array{phase: string, goal: string, actions: array<int, string>, rollback_signal: string}>
     */
    private function rolloutPlanFor(array $input, string $selectedModel): array
    {
        $plan = [
            [
                'phase' => 'instrument',
                'goal' => 'Measure the current JWT lifecycle before enforcing new rejection behavior.',
                'actions' => [
                    'Add jti and exp to newly issued tokens.',
                    'Log token issue, refresh, logout, revoke, and reject events without raw token values.',
                    'Track how often protected routes receive expired, malformed, or unknown tokens.',
                ],
                'rollback_signal' => 'Unexpected auth error rate rises before middleware enforcement is enabled.',
            ],
            [
                'phase' => 'dual-read',
                'goal' => "Read {$selectedModel} state without blocking requests yet.",
                'actions' => [
                    "Create {$input['revocation_store']} records for new logout and security events.",
                    'Compare token claims with revocation state and emit audit-only warnings.',
                    'Verify that pruning keeps revocation records bounded after token expiry.',
                ],
                'rollback_signal' => 'Audit-only checks produce false positives for valid active sessions.',
            ],
            [
                'phase' => 'enforce',
                'goal' => 'Reject revoked tokens on a controlled traffic slice.',
                'actions' => [
                    'Enable middleware enforcement for internal users or a small percentage of traffic.',
                    'Return 401 for valid-signature tokens whose jti, version, or refresh family is revoked.',
                    'Monitor login, refresh, logout, 401, and support-ticket rates.',
                ],
                'rollback_signal' => 'Legitimate users are logged out unexpectedly or refresh success drops sharply.',
            ],
            [
                'phase' => 'harden',
                'goal' => 'Make revocation part of normal auth operations.',
                'actions' => [
                    'Enable enforcement for all protected routes.',
                    'Document incident playbooks for lost device, password change, role downgrade, and token reuse.',
                    'Add scheduled cleanup, dashboards, and alert thresholds for revocation failures.',
                ],
                'rollback_signal' => 'Revocation store latency or availability becomes a bottleneck for protected routes.',
            ],
        ];

        if ($input['refresh_rotation'] === 'yes') {
            $plan[1]['actions'][] = 'Run refresh-token rotation in compatibility mode before rejecting reused refresh tokens.';
            $plan[2]['actions'][] = 'Revoke the refresh-token family when an old refresh token is reused.';
        }

        return $plan;
    }

    /**
     * Score the revocation risk so learners can compare scenarios quickly.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array{score: int, level: string, reasons: array<int, string>}
     */
    private function riskScoreFor(array $input, string $selectedModel): array
    {
        $score = 20;
        $reasons = [];

        if ($input['token_lifetime'] === 'days') {
            $score += 35;
            $reasons[] = 'Day-long access tokens create a large abuse window after token leakage.';
        } elseif ($input['token_lifetime'] === 'hours') {
            $score += 15;
            $reasons[] = 'Hour-long access tokens need clear logout, refresh, and role-change behavior.';
        }

        if ($input['immediate_logout'] === 'yes' && $selectedModel === 'short-lived-access-token') {
            $score += 30;
            $reasons[] = 'Immediate logout cannot rely only on waiting for a short access-token expiry.';
        }

        if ($input['refresh_rotation'] === 'no') {
            $score += 20;
            $reasons[] = 'Without refresh-token rotation, stolen refresh credentials are harder to contain.';
        } else {
            $score -= 10;
            $reasons[] = 'Refresh-token rotation reduces future access after logout, reuse, or compromise.';
        }

        if ($input['revocation_store'] === 'cache') {
            $score += 10;
            $reasons[] = 'Cache-backed revocation must account for eviction so revoked tokens are not silently accepted.';
        }

        if (in_array($selectedModel, ['denylist', 'token-version'], true)) {
            $score -= 10;
            $reasons[] = "{$selectedModel} adds server-side enforcement before authorization.";
        }

        $score = max(0, min(100, $score));

        return [
            'score' => $score,
            'level' => $this->riskLevelFor($score),
            'reasons' => $reasons === [] ? ['The selected revocation plan has no major warning signal.'] : $reasons,
        ];
    }

    /**
     * Convert a numeric revocation score into a readable level.
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
     * Explain why the selected revocation strategy fits the submitted signals.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array<int, array{signal: string, value: string, decision_impact: string}>
     */
    private function decisionMatrixFor(array $input, string $selectedModel): array
    {
        return [
            [
                'signal' => 'Client type',
                'value' => $input['client_type'],
                'decision_impact' => $this->clientTypeImpact($input['client_type']),
            ],
            [
                'signal' => 'Requested model',
                'value' => $input['revocation_model'],
                'decision_impact' => $input['revocation_model'] === 'auto'
                    ? "Auto mode selected {$selectedModel} from the logout and lifetime signals."
                    : "The plan keeps {$selectedModel} and explains its API, database, and middleware costs.",
            ],
            [
                'signal' => 'Token lifetime',
                'value' => $input['token_lifetime'],
                'decision_impact' => $this->tokenLifetimeImpact($input['token_lifetime']),
            ],
            [
                'signal' => 'Revocation store',
                'value' => $input['revocation_store'],
                'decision_impact' => $this->revocationStoreImpact($input['revocation_store']),
            ],
            [
                'signal' => 'Immediate logout',
                'value' => $input['immediate_logout'],
                'decision_impact' => $input['immediate_logout'] === 'yes'
                    ? 'Immediate logout requires server-side state such as a denylist, token version, or revoked refresh-token family.'
                    : 'Without immediate logout, very short access-token lifetime may be acceptable for lower-risk systems.',
            ],
            [
                'signal' => 'Refresh rotation',
                'value' => $input['refresh_rotation'],
                'decision_impact' => $input['refresh_rotation'] === 'yes'
                    ? 'Refresh rotation lets the system revoke future access and detect stolen refresh tokens.'
                    : 'Without refresh rotation, leaked long-lived credentials are harder to contain.',
            ],
        ];
    }

    /**
     * Explain the revocation impact of a client type.
     */
    private function clientTypeImpact(string $clientType): string
    {
        return match ($clientType) {
            'browser-spa' => 'Browser clients need revocation reviewed together with cookie, CSRF, XSS, and logout behavior.',
            'mobile-app' => 'Mobile apps need device-aware revocation because lost devices and secure platform storage are part of the threat model.',
            'server-api' => 'Server-to-server APIs usually need auditability, scoped tokens, and predictable token-version or denylist checks.',
            default => 'Third-party API consumers need clear token expiry, rotation, scopes, and revocation notices.',
        };
    }

    /**
     * Explain how token lifetime changes the revocation decision.
     */
    private function tokenLifetimeImpact(string $tokenLifetime): string
    {
        return match ($tokenLifetime) {
            'days' => 'Day-long access tokens create a large exposure window, so revocation checks should be explicit.',
            'hours' => 'Hour-long tokens need logout, refresh, and role-change behavior documented and tested.',
            default => 'Minute-long access tokens reduce exposure, but refresh tokens still need rotation and revocation.',
        };
    }

    /**
     * Explain the operational impact of the revocation store.
     */
    private function revocationStoreImpact(string $revocationStore): string
    {
        return match ($revocationStore) {
            'redis' => 'Redis gives fast jti lookup and TTL pruning, but persistence and failover behavior must be understood.',
            'database' => 'Database storage gives auditability and simple inspection, but hot auth paths may need caching.',
            default => 'Cache storage can be enough for low-risk revocation, but eviction behavior must not silently allow revoked tokens.',
        };
    }

    /**
     * Choose the main revocation strategy.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array<string, string>
     */
    private function strategyFor(array $input): array
    {
        $model = $input['revocation_model'];

        if ($model === 'auto') {
            $model = $input['immediate_logout'] === 'yes' ? 'denylist' : 'short-lived-access-token';
        }

        return match ($model) {
            'token-version' => [
                'model' => 'token-version',
                'label' => 'Use a user token version or security version',
                'reason' => 'Store a version on the user or session record and reject JWTs whose version no longer matches after password changes, role changes, or account suspension.',
            ],
            'refresh-rotation' => [
                'model' => 'refresh-rotation',
                'label' => 'Use short-lived access tokens with refresh-token rotation',
                'reason' => 'Do not try to revoke every access token alone. Keep access tokens short and revoke the refresh-token family when logout, reuse, or compromise is detected.',
            ],
            'short-lived-access-token' => [
                'model' => 'short-lived-access-token',
                'label' => 'Use very short-lived access tokens',
                'reason' => 'For lower-risk APIs, reduce the damage window and avoid a central lookup on every request, while still revoking refresh tokens and privileged sessions.',
            ],
            default => [
                'model' => 'denylist',
                'label' => 'Use a denylist keyed by JWT ID',
                'reason' => 'A stateless JWT cannot be taken back unless the server checks extra state. Store the token jti until expiry and reject it in auth middleware.',
            ],
        };
    }

    /**
     * Return database or cache records required by the selected plan.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array<int, string>
     */
    private function databaseSchemaFor(array $input): array
    {
        $schema = [
            'Add a unique jti claim to every issued JWT so a single token can be identified without storing the raw token.',
            'Store only token identifiers, hashes, user IDs, device IDs, issued_at, revoked_at, reason, and expires_at metadata.',
        ];

        if (in_array($input['revocation_model'], ['denylist', 'auto'], true)) {
            $schema[] = "Create revoked_tokens with jti primary key, user_id index, reason, revoked_at, and expires_at; prune rows after expiry from {$input['revocation_store']}.";
        }

        if ($input['revocation_model'] === 'token-version') {
            $schema[] = 'Add token_version or security_version to users or user_sessions and include the version claim when issuing JWTs.';
        }

        if ($input['refresh_rotation'] === 'yes') {
            $schema[] = 'Create refresh_token_families with current_token_hash, replaced_by_hash, revoked_at, reuse_detected_at, user_id, device_id, and expires_at.';
        }

        return $schema;
    }

    /**
     * Return API endpoints learners should design.
     *
     * @return array<int, array{method: string, path: string, purpose: string}>
     */
    private function apiEndpointsFor(array $input): array
    {
        $endpoints = [
            ['method' => 'POST', 'path' => '/api/auth/login', 'purpose' => 'Issue access token with jti, exp, iat, user_id, scopes, and optional version claim.'],
            ['method' => 'POST', 'path' => '/api/auth/logout', 'purpose' => 'Revoke the current token or refresh-token family and clear browser/device credentials.'],
            ['method' => 'POST', 'path' => '/api/auth/refresh', 'purpose' => 'Rotate refresh token, issue a new short-lived access token, and detect token reuse.'],
        ];

        if ($input['immediate_logout'] === 'yes') {
            $endpoints[] = ['method' => 'POST', 'path' => '/api/auth/logout-all', 'purpose' => 'Invalidate every session for the user after password change, device loss, or account risk.'];
        }

        return $endpoints;
    }

    /**
     * Return the request-time checks for middleware.
     *
     * @return array<int, string>
     */
    private function middlewareFlowFor(array $input): array
    {
        $flow = [
            'Parse and verify signature, issuer, audience, nbf, iat, and exp before trusting claims.',
            'Read jti and user_id from the verified token only.',
        ];

        if (in_array($input['revocation_model'], ['denylist', 'auto'], true)) {
            $flow[] = "Check {$input['revocation_store']} for the jti and reject the request with 401 when it is revoked.";
        }

        if ($input['revocation_model'] === 'token-version') {
            $flow[] = 'Compare the token version claim with the user or session security version stored server-side.';
        }

        $flow[] = 'Authorize scopes and abilities after the token is verified and not revoked.';
        $flow[] = 'Log token lifecycle events without recording raw bearer values.';

        return $flow;
    }

    /**
     * Return concrete implementation steps.
     *
     * @return array<int, string>
     */
    private function revocationStepsFor(array $input): array
    {
        return [
            'Add jti and exp to access tokens and make the values visible in tests.',
            'On logout, insert the current jti into the revocation store until exp, or revoke the refresh-token family.',
            'On password change, role downgrade, account lock, or device loss, revoke all refresh-token families and bump token_version when using versioned tokens.',
            'Add middleware that checks expiry first, then revocation state, then authorization policy or scope.',
            'Schedule pruning for expired revoked token records so the revocation table or cache stays bounded.',
        ];
    }

    /**
     * Return review questions to run before shipping the revocation design.
     *
     * @param  array{client_type: string, revocation_model: string, token_lifetime: string, revocation_store: string, immediate_logout: string, refresh_rotation: string}  $input
     * @return array<int, array{area: string, question: string, pass_signal: string}>
     */
    private function reviewChecklistFor(array $input, string $selectedModel): array
    {
        $checklist = [
            [
                'area' => 'token identity',
                'question' => 'Does every access token include a unique jti and bounded exp claim?',
                'pass_signal' => 'Tests can revoke one token without revoking unrelated valid tokens.',
            ],
            [
                'area' => 'server-side state',
                'question' => "Is {$selectedModel} checked in middleware before authorization decisions?",
                'pass_signal' => 'A token with valid signature but revoked state is rejected with 401.',
            ],
            [
                'area' => 'data retention',
                'question' => 'Are revoked token records pruned only after the original token expiry?',
                'pass_signal' => 'Expired revocation records are removed by schedule, queue, TTL, or database job.',
            ],
            [
                'area' => 'observability',
                'question' => 'Can the team audit issue, refresh, revoke, reuse, and reject events without exposing raw tokens?',
                'pass_signal' => 'Logs include event type, user ID, device ID, jti hash, and reason without bearer strings.',
            ],
        ];

        if ($input['refresh_rotation'] === 'yes') {
            $checklist[] = [
                'area' => 'refresh reuse',
                'question' => 'Does refresh-token reuse revoke the token family and alert the auth pipeline?',
                'pass_signal' => 'A test proves an old refresh token cannot mint a new access token after rotation.',
            ];
        }

        return $checklist;
    }

    /**
     * Return risk notes for the selected context.
     *
     * @return array<int, string>
     */
    private function riskNotesFor(array $input): array
    {
        $notes = [
            'A signed JWT stays valid until exp unless the server performs an additional lookup or changes verification state.',
            'Long-lived access tokens increase the window where a leaked token can be used.',
        ];

        if ($input['token_lifetime'] === 'days') {
            $notes[] = 'Day-long access tokens should trigger denylist checks or a redesign toward shorter access tokens.';
        }

        if ($input['client_type'] === 'browser-spa') {
            $notes[] = 'Browser clients need token storage and CSRF/XSS decisions reviewed together with revocation behavior.';
        }

        return $notes;
    }

    /**
     * Return small implementation snippets for the selected plan.
     *
     * @return array<string, string>
     */
    private function implementationSnippetsFor(array $input): array
    {
        return [
            'migration' => "Schema::create('revoked_tokens', function (Blueprint \$table) {\n    \$table->string('jti')->primary();\n    \$table->foreignId('user_id')->index();\n    \$table->timestamp('revoked_at');\n    \$table->timestamp('expires_at')->index();\n    \$table->string('reason')->nullable();\n});",
            'middleware' => "if (\$revokedTokens->contains(\$token->jti())) {\n    throw new AuthenticationException('Token has been revoked.');\n}",
            'logout' => "\$revokedTokens->revoke(jti: \$token->jti(), userId: \$user->id, expiresAt: \$token->expiresAt(), reason: 'logout');",
            'store' => "Use {$input['revocation_store']} for fast jti lookup and keep database records when audit history is required.",
        ];
    }

    /**
     * Return verification cases for the plan.
     *
     * @return array<int, string>
     */
    private function testsFor(array $input): array
    {
        $tests = [
            'A valid non-revoked JWT can access the protected route.',
            'A revoked jti is rejected with 401 even when signature and exp are valid.',
            'Expired revoked-token records are pruned after their expires_at value.',
            'Logout does not log raw token values.',
        ];

        if ($input['refresh_rotation'] === 'yes') {
            $tests[] = 'Reusing an old refresh token revokes the token family and blocks future refresh attempts.';
        }

        return $tests;
    }

    /**
     * Return an interview-ready answer for the selected model.
     */
    private function interviewAnswerFor(string $model): string
    {
        if ($model === 'token-version') {
            return 'A JWT is stateless, so I cannot truly revoke it without checking server-side state. For user-wide invalidation, I would store a token version on the user or session and reject tokens whose version is stale.';
        }

        if ($model === 'short-lived-access-token' || $model === 'refresh-rotation') {
            return 'I keep access tokens short-lived, rotate refresh tokens, revoke refresh-token families on logout or reuse, and accept that access-token exposure is bounded by a small expiry window.';
        }

        return 'A JWT cannot be pulled back by magic before expiry. To revoke it, issue every token with a jti, store revoked jtis until exp, check that store in middleware, and combine it with short access-token lifetime and refresh-token rotation.';
    }
}
