<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class OauthFlowPlanService
{
    /**
     * Build an OAuth flow recommendation from client and security signals.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array{recommendation: array{flow: string, label: string, reason: string, migration_needed: bool}, architecture_decision_record: array{status: string, decision: string, context: string, consequences: array<int, string>}, compatibility_notes: array<int, array{client_type: string, recommended_flow: string, note: string}>, flow_comparison: array<int, array{area: string, implicit: string, authorization_code_pkce: string}>, risk_score: array{score: int, level: string, reasons: array<int, string>}, threat_model: array<int, array{asset: string, threat: string, mitigation: string}>, provider_capability_matrix: array<int, array{capability: string, required: string, current_status: string, action: string}>, why_implicit_removed: array<int, string>, sequence_steps: array<int, array{step: string, implicit: string, authorization_code_pkce: string}>, callback_validation_rules: array<int, array{rule: string, reject_when: string, evidence: string}>, pkce_checklist: array<int, string>, pkce_sequence_simulation: array{applies: bool, title: string, summary: string, steps: array<int, array{stage: string, actor: string, sends: string, stores: string, security_point: string}>}, pkce_failure_drill: array<int, array{case: string, signal: string, response: string, test: string}>, token_lifetime_policy: array{access_token: string, refresh_token: string, rotation: string, review_note: string}, scope_consent_matrix: array<int, array{scope_area: string, risk: string, review_rule: string}>, authorize_request_hardening: array<int, array{parameter: string, required_rule: string, reject_when: string}>, token_endpoint_contract: array<int, array{field: string, rule: string, failure_response: string}>, frontend_cleanup_plan: array<int, array{surface: string, cleanup_action: string, verification: string}>, id_token_validation_rules: array<int, array{claim: string, rule: string, reject_when: string}>, implementation_snippets: array<string, string>, migration_plan: array<int, string>, rollout_plan: array<int, array{phase: string, action: string, exit_signal: string}>, client_cutover_checklist: array<int, array{item: string, owner: string, done_when: string}>, observability_plan: array{metrics: array<int, string>, log_events: array<int, string>, alerts: array<int, string>}, failure_modes: array<int, array{failure: string, symptom: string, response: string}>, incident_response_playbook: array<int, array{phase: string, action: string, evidence: string}>, deprecation_policy: array<int, array{rule: string, applies_to: string, enforcement: string}>, review_checklist: array<int, array{area: string, question: string, pass_signal: string}>, security_test_matrix: array<int, array{category: string, scenario: string, expected_result: string}>, tests: array<int, string>, interview_answer: string, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $recommendation = $this->recommendationFor($input);

        return [
            'recommendation' => $recommendation,
            'architecture_decision_record' => $this->architectureDecisionRecordFor($input, $recommendation),
            'compatibility_notes' => $this->compatibilityNotesFor($input, $recommendation),
            'flow_comparison' => $this->flowComparison(),
            'risk_score' => $this->riskScoreFor($input),
            'threat_model' => $this->threatModelFor($input),
            'provider_capability_matrix' => $this->providerCapabilityMatrixFor($input),
            'why_implicit_removed' => $this->whyImplicitRemoved(),
            'sequence_steps' => $this->sequenceSteps(),
            'callback_validation_rules' => $this->callbackValidationRulesFor($input),
            'pkce_checklist' => $this->pkceChecklistFor($input),
            'pkce_sequence_simulation' => $this->pkceSequenceSimulationFor($input, $recommendation),
            'pkce_failure_drill' => $this->pkceFailureDrillFor($input),
            'token_lifetime_policy' => $this->tokenLifetimePolicyFor($input),
            'scope_consent_matrix' => $this->scopeConsentMatrixFor($input),
            'authorize_request_hardening' => $this->authorizeRequestHardeningFor($input),
            'token_endpoint_contract' => $this->tokenEndpointContractFor($input),
            'frontend_cleanup_plan' => $this->frontendCleanupPlanFor($input),
            'id_token_validation_rules' => $this->idTokenValidationRulesFor($input),
            'implementation_snippets' => $this->implementationSnippets(),
            'client_credentials_checklist' => $this->clientCredentialsChecklistFor($input, $recommendation),
            'client_credentials_access_contract' => $this->clientCredentialsAccessContractFor($input, $recommendation),
            'client_credentials_token_validation_policy' => $this->clientCredentialsTokenValidationPolicyFor($input, $recommendation),
            'client_credentials_operational_plan' => $this->clientCredentialsOperationalPlanFor($input, $recommendation),
            'client_credentials_rotation_drill' => $this->clientCredentialsRotationDrillFor($input, $recommendation),
            'client_credentials_monitoring_signals' => $this->clientCredentialsMonitoringSignalsFor($input, $recommendation),
            'client_credentials_failure_playbook' => $this->clientCredentialsFailurePlaybookFor($input, $recommendation),
            'migration_plan' => $this->migrationPlanFor($input, $recommendation['flow']),
            'rollout_plan' => $this->rolloutPlanFor($input, $recommendation),
            'client_cutover_checklist' => $this->clientCutoverChecklistFor($input, $recommendation),
            'observability_plan' => $this->observabilityPlanFor($input),
            'failure_modes' => $this->failureModesFor($input),
            'incident_response_playbook' => $this->incidentResponsePlaybookFor($input),
            'deprecation_policy' => $this->deprecationPolicyFor($input),
            'review_checklist' => $this->reviewChecklistFor($input),
            'security_test_matrix' => $this->securityTestMatrixFor($input),
            'tests' => $this->testsFor($recommendation['flow']),
            'client_credentials_security_review_rubric' => $this->clientCredentialsSecurityReviewRubricFor($input, $recommendation),
            'client_credentials_interview_checklist' => $this->clientCredentialsInterviewChecklistFor($input, $recommendation),
            'interview_answer' => $this->interviewAnswerFor($input, $recommendation['flow']),
            'commands' => [
                'php artisan route:list --path=oauth-flow-plan',
                'php artisan test --filter OauthFlowPlan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Choose the recommended OAuth flow for the submitted context.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array{flow: string, label: string, reason: string, migration_needed: bool}
     */
    private function recommendationFor(array $input): array
    {
        if ($input['client_type'] === 'service-to-service' && $input['can_keep_secret'] === 'yes') {
            return [
                'flow' => 'client-credentials',
                'label' => 'Use Client Credentials Flow for service-to-service access',
                'reason' => 'A backend service can authenticate as itself at the token endpoint and receive scoped access without a user login or browser redirect.',
                'migration_needed' => $input['current_flow'] !== 'client-credentials',
            ];
        }

        if ($input['client_type'] === 'server-rendered-web' && $input['can_keep_secret'] === 'yes') {
            return [
                'flow' => 'authorization-code-confidential-client',
                'label' => 'Use Authorization Code flow with a confidential backend client',
                'reason' => 'A server-rendered backend can keep a client secret and exchange the authorization code away from the browser.',
                'migration_needed' => $input['current_flow'] !== 'authorization-code',
            ];
        }

        if ($input['pkce_supported'] === 'yes') {
            return [
                'flow' => 'authorization-code-pkce',
                'label' => 'Use Authorization Code flow with PKCE',
                'reason' => 'Public clients such as SPAs and mobile apps cannot keep a secret, so PKCE binds the code exchange to the original client without exposing tokens in the URL fragment.',
                'migration_needed' => $input['current_flow'] !== 'authorization-code-pkce',
            ];
        }

        return [
            'flow' => 'block-until-pkce-supported',
            'label' => 'Block Implicit and add PKCE support before production',
            'reason' => 'Implicit returns tokens through the front channel. Without PKCE support, the safer choice is to upgrade the authorization server or client library first.',
            'migration_needed' => true,
        ];
    }

    /**
     * Return an ADR-style summary for the OAuth flow decision.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array{status: string, decision: string, context: string, consequences: array<int, string>}
     */
    private function architectureDecisionRecordFor(array $input, array $recommendation): array
    {
        return [
            'status' => $recommendation['migration_needed'] ? 'proposed-migration' : 'accepted',
            'decision' => "Use {$recommendation['flow']} instead of OAuth Implicit Flow for {$input['client_type']}.",
            'context' => 'Implicit Flow exposes tokens through browser-facing redirects, while Authorization Code with PKCE or a confidential backend keeps tokens out of callback URLs.',
            'consequences' => [
                'Clients must use response_type=code instead of response_type=token.',
                'Callbacks must validate state and reject token-bearing URL input.',
                'Public clients must use PKCE S256 and short-lived verifier state.',
                'Operational telemetry must prove legacy Implicit callbacks have reached zero before removal.',
            ],
        ];
    }

    /**
     * Return compatibility notes for client families.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array<int, array{client_type: string, recommended_flow: string, note: string}>
     */
    private function compatibilityNotesFor(array $input, array $recommendation): array
    {
        return [
            [
                'client_type' => 'browser SPA',
                'recommended_flow' => 'authorization-code-pkce',
                'note' => 'SPAs are public clients, so they should use PKCE and avoid tokens in callback URLs.',
            ],
            [
                'client_type' => 'mobile app',
                'recommended_flow' => 'authorization-code-pkce',
                'note' => 'Mobile apps are public clients too; use PKCE plus platform-safe token storage and server-side revocation controls.',
            ],
            [
                'client_type' => 'server-rendered web',
                'recommended_flow' => 'authorization-code-confidential-client',
                'note' => 'Backend web apps can keep a client secret and should exchange codes server-side.',
            ],
            [
                'client_type' => 'service-to-service',
                'recommended_flow' => 'client-credentials',
                'note' => 'Machine-to-machine clients should authenticate as the service itself and use scoped access tokens with audience validation.',
            ],
            [
                'client_type' => $input['client_type'],
                'recommended_flow' => $recommendation['flow'],
                'note' => $recommendation['reason'],
            ],
        ];
    }

    /**
     * Compare Implicit and Authorization Code with PKCE.
     *
     * @return array<int, array{area: string, implicit: string, authorization_code_pkce: string}>
     */
    private function flowComparison(): array
    {
        return [
            [
                'area' => 'token delivery',
                'implicit' => 'The access token is returned directly to the browser through the front channel.',
                'authorization_code_pkce' => 'The browser receives only a short-lived authorization code, then exchanges it with a code verifier.',
            ],
            [
                'area' => 'URL exposure',
                'implicit' => 'Tokens can leak through browser history, redirects, extensions, screenshots, logs, or referrer mistakes.',
                'authorization_code_pkce' => 'The token is not placed in the redirect URL; the code is one-time and bound to PKCE.',
            ],
            [
                'area' => 'public client safety',
                'implicit' => 'It was designed before modern browser and mobile guidance made PKCE widely available.',
                'authorization_code_pkce' => 'PKCE protects public clients that cannot store a client secret.',
            ],
            [
                'area' => 'refresh and rotation',
                'implicit' => 'It pushes teams toward long-lived access tokens because refresh-token handling is awkward in browsers.',
                'authorization_code_pkce' => 'It works with short-lived access tokens and controlled refresh-token rotation.',
            ],
        ];
    }

    /**
     * Score the risk of keeping the current OAuth flow.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array{score: int, level: string, reasons: array<int, string>}
     */
    private function riskScoreFor(array $input): array
    {
        $score = 20;
        $reasons = [];

        if ($input['current_flow'] === 'implicit') {
            $score += 35;
            $reasons[] = 'Implicit flow exposes access tokens through the browser front channel.';
        }

        if ($input['browser_history_risk'] === 'high') {
            $score += 25;
            $reasons[] = 'High browser-history or redirect leakage risk makes front-channel tokens unsafe.';
        } elseif ($input['browser_history_risk'] === 'medium') {
            $score += 15;
            $reasons[] = 'Medium browser leakage risk still requires avoiding tokens in URLs.';
        }

        if ($input['pkce_supported'] === 'no') {
            $score += 20;
            $reasons[] = 'Without PKCE, public clients have no modern proof-of-possession check for the code exchange.';
        }

        if ($input['refresh_token_needed'] === 'yes' && $input['current_flow'] === 'implicit') {
            $score += 15;
            $reasons[] = 'Refresh-token needs should be handled with rotation, not by stretching implicit access-token lifetime.';
        }

        $score = max(0, min(100, $score));

        return [
            'score' => $score,
            'level' => $score >= 70 ? 'high' : ($score >= 40 ? 'medium' : 'controlled'),
            'reasons' => $reasons === [] ? ['The selected context has no major OAuth flow warning signals.'] : $reasons,
        ];
    }

    /**
     * Return the main technical reasons Implicit is no longer recommended.
     *
     * @return array<int, string>
     */
    private function whyImplicitRemoved(): array
    {
        return [
            'It returns tokens through the browser front channel, where URLs and fragments are easier to expose.',
            'Modern browsers, SPAs, and mobile apps can use Authorization Code with PKCE instead of needing Implicit as a workaround.',
            'PKCE prevents a stolen authorization code from being exchanged without the original code verifier.',
            'Short-lived access tokens plus refresh-token rotation are easier to model safely with Authorization Code than with Implicit.',
            'Security guidance moved away from token-in-URL patterns because real systems have logs, extensions, redirects, crash reports, and user support screenshots.',
        ];
    }

    /**
     * Return OAuth-specific assets, threats, and mitigations.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{asset: string, threat: string, mitigation: string}>
     */
    private function threatModelFor(array $input): array
    {
        $threats = [
            [
                'asset' => 'access token',
                'threat' => 'Token appears in front-channel URLs, browser tooling, redirect chains, extensions, logs, or screenshots.',
                'mitigation' => 'Use response_type=code and exchange the code through Authorization Code with PKCE or a backend confidential client.',
            ],
            [
                'asset' => 'authorization code',
                'threat' => 'A code is intercepted and replayed by a different client.',
                'mitigation' => 'Bind the code to a high-entropy PKCE code_verifier and reject reused or expired codes.',
            ],
            [
                'asset' => 'login callback',
                'threat' => 'CSRF or login substitution through missing state validation.',
                'mitigation' => 'Generate per-login state, store it server-side or in protected client state, and reject mismatches.',
            ],
        ];

        if ($input['refresh_token_needed'] === 'yes') {
            $threats[] = [
                'asset' => 'refresh token',
                'threat' => 'A long-lived refresh credential is reused after theft.',
                'mitigation' => 'Rotate refresh tokens, detect reuse, revoke the token family, and log suspicious reuse metadata.',
            ];
        }

        return $threats;
    }

    /**
     * Return the provider and client capabilities needed before migration.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{capability: string, required: string, current_status: string, action: string}>
     */
    private function providerCapabilityMatrixFor(array $input): array
    {
        return [
            [
                'capability' => 'PKCE S256',
                'required' => 'Public browser and mobile clients must support code_challenge_method=S256.',
                'current_status' => $input['pkce_supported'] === 'yes' ? 'available' : 'missing',
                'action' => $input['pkce_supported'] === 'yes'
                    ? 'Require PKCE on every public-client authorization request.'
                    : 'Upgrade the provider or client library before allowing production migration.',
            ],
            [
                'capability' => 'state validation',
                'required' => 'Every login attempt needs a nonce-like state value bound to the callback.',
                'current_status' => 'must-verify',
                'action' => 'Add callback tests for missing, mismatched, expired, and replayed state.',
            ],
            [
                'capability' => 'front-channel token blocking',
                'required' => 'Callbacks must reject access_token, id_token, token_type, and expires_in in URL input.',
                'current_status' => $input['current_flow'] === 'implicit' ? 'gap' : 'must-verify',
                'action' => 'Reject token-bearing callback URLs and remove response_type=token from client config.',
            ],
            [
                'capability' => 'refresh-token rotation',
                'required' => $input['refresh_token_needed'] === 'yes'
                    ? 'Refresh-token reuse detection and family revocation are required.'
                    : 'Short token lifetime and re-authentication path are acceptable.',
                'current_status' => $input['refresh_token_needed'] === 'yes' ? 'must-verify' : 'not-required',
                'action' => $input['refresh_token_needed'] === 'yes'
                    ? 'Verify rotation, reuse detection, revocation, and audit logging.'
                    : 'Document expiry UX and avoid adding long-lived refresh credentials.',
            ],
        ];
    }

    /**
     * Return side-by-side sequence steps for both flows.
     *
     * @return array<int, array{step: string, implicit: string, authorization_code_pkce: string}>
     */
    private function sequenceSteps(): array
    {
        return [
            [
                'step' => '1. Start login',
                'implicit' => 'SPA redirects to /authorize with response_type=token.',
                'authorization_code_pkce' => 'Client creates code_verifier, sends code_challenge, and redirects with response_type=code.',
            ],
            [
                'step' => '2. User authenticates',
                'implicit' => 'Authorization server authenticates the user and approves scopes.',
                'authorization_code_pkce' => 'Authorization server authenticates the user and records the code_challenge.',
            ],
            [
                'step' => '3. Redirect back',
                'implicit' => 'Redirect URI receives access_token in the URL fragment.',
                'authorization_code_pkce' => 'Redirect URI receives a short-lived authorization code.',
            ],
            [
                'step' => '4. Token handling',
                'implicit' => 'JavaScript parses the token from the URL and stores or uses it directly.',
                'authorization_code_pkce' => 'Client exchanges code plus code_verifier at /token and receives tokens after verification.',
            ],
        ];
    }

    /**
     * Return validation rules for OAuth callback handling.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{rule: string, reject_when: string, evidence: string}>
     */
    private function callbackValidationRulesFor(array $input): array
    {
        $rules = [
            [
                'rule' => 'require authorization code',
                'reject_when' => 'The callback contains access_token, id_token, token_type, or expires_in instead of code.',
                'evidence' => 'Feature test rejects token-bearing callback URLs.',
            ],
            [
                'rule' => 'validate state',
                'reject_when' => 'The state parameter is missing, expired, already used, or does not match the stored login attempt.',
                'evidence' => 'State mismatch and replay tests fail before token exchange.',
            ],
            [
                'rule' => 'verify redirect URI',
                'reject_when' => 'The callback redirect_uri differs from the registered and originally requested URI.',
                'evidence' => 'Token exchange uses the exact registered redirect URI.',
            ],
        ];

        if ($input['can_keep_secret'] === 'no') {
            $rules[] = [
                'rule' => 'require PKCE verifier',
                'reject_when' => 'The code_verifier is missing, too short, reused, or does not match the original code_challenge.',
                'evidence' => 'Wrong verifier test is rejected by the token endpoint.',
            ];
        }

        return $rules;
    }

    /**
     * Return PKCE implementation checks.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, string>
     */
    private function pkceChecklistFor(array $input): array
    {
        $checks = [
            'Generate a high-entropy code_verifier per login attempt.',
            'Create code_challenge with S256, not plain, unless the provider has no S256 support.',
            'Store code_verifier only long enough to complete the callback.',
            'Send response_type=code with code_challenge and code_challenge_method=S256.',
            'Exchange code plus code_verifier at the token endpoint and clear verifier state after use.',
        ];

        if ($input['pkce_supported'] === 'no') {
            $checks[] = 'Upgrade the OAuth provider or client library before allowing production browser or mobile login.';
        }

        return $checks;
    }

    /**
     * Return a step-by-step PKCE simulation for the selected client.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array{applies: bool, title: string, summary: string, steps: array<int, array{stage: string, actor: string, sends: string, stores: string, security_point: string}>}
     */
    private function pkceSequenceSimulationFor(array $input, array $recommendation): array
    {
        $applies = in_array($recommendation['flow'], ['authorization-code-pkce', 'block-until-pkce-supported'], true)
            || $input['can_keep_secret'] === 'no';

        if (! $applies) {
            return [
                'applies' => false,
                'title' => 'PKCE is optional for this confidential-client scenario.',
                'summary' => 'The backend can keep a client secret, but the team may still enable PKCE as defense in depth if the provider supports it.',
                'steps' => [],
            ];
        }

        return [
            'applies' => true,
            'title' => 'Authorization Code with PKCE sequence',
            'summary' => 'The browser or app receives only an authorization code. The token endpoint releases tokens only when the original code_verifier proves this is the same login attempt.',
            'steps' => [
                [
                    'stage' => '1. Create verifier',
                    'actor' => $input['client_type'],
                    'sends' => 'Nothing leaves the client yet.',
                    'stores' => 'Store high-entropy code_verifier for this login attempt only.',
                    'security_point' => 'The verifier is the private proof; do not log it or persist it beyond callback handling.',
                ],
                [
                    'stage' => '2. Start authorize request',
                    'actor' => $input['client_type'],
                    'sends' => 'response_type=code, code_challenge=S256(code_verifier), code_challenge_method=S256, state, redirect_uri, and least-privilege scope.',
                    'stores' => 'Authorization server stores the code_challenge with the pending authorization code.',
                    'security_point' => 'The real verifier is not sent through the browser redirect.',
                ],
                [
                    'stage' => '3. Receive callback',
                    'actor' => 'redirect URI handler',
                    'sends' => 'Callback receives code and state, not access_token.',
                    'stores' => 'Match callback state to the stored login attempt and load the verifier.',
                    'security_point' => 'Reject token-bearing callbacks and state mismatches before token exchange.',
                ],
                [
                    'stage' => '4. Exchange code',
                    'actor' => 'token endpoint client',
                    'sends' => 'grant_type=authorization_code, code, redirect_uri, client_id, and code_verifier.',
                    'stores' => 'Store returned tokens according to client type, then clear verifier state.',
                    'security_point' => 'A stolen code fails without the original verifier.',
                ],
            ],
        ];
    }

    /**
     * Return practical PKCE failure drills for review and tests.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{case: string, signal: string, response: string, test: string}>
     */
    private function pkceFailureDrillFor(array $input): array
    {
        $drills = [
            [
                'case' => 'missing verifier',
                'signal' => 'Token endpoint receives authorization_code grant without code_verifier for a public client.',
                'response' => 'Reject the exchange and ask the client to restart login; do not retry with a blank verifier.',
                'test' => 'Feature test posts code without code_verifier and expects token exchange rejection.',
            ],
            [
                'case' => 'wrong verifier',
                'signal' => 'code_verifier hash does not match the stored S256 code_challenge.',
                'response' => 'Reject the exchange, clear login attempt state, and record oauth_pkce_verifier_failed without raw verifier.',
                'test' => 'Feature test exchanges a valid code with a different verifier and expects failure.',
            ],
            [
                'case' => 'reused code',
                'signal' => 'The same authorization code is submitted again after successful exchange.',
                'response' => 'Reject as replay, revoke risky token family if reuse indicates compromise, and alert on repeated attempts.',
                'test' => 'Feature test exchanges one code twice and proves the second attempt fails.',
            ],
        ];

        if ($input['pkce_supported'] === 'no') {
            $drills[] = [
                'case' => 'provider lacks PKCE',
                'signal' => 'Provider or client library cannot send or verify S256 code_challenge.',
                'response' => 'Block production rollout for public clients until provider or SDK support is upgraded.',
                'test' => 'Acceptance test keeps public-client login disabled when PKCE support is missing.',
            ];
        }

        return $drills;
    }

    /**
     * Return the token lifetime policy that prevents Implicit-style long-lived access tokens.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array{access_token: string, refresh_token: string, rotation: string, review_note: string}
     */
    private function tokenLifetimePolicyFor(array $input): array
    {
        return [
            'access_token' => $input['current_flow'] === 'implicit'
                ? 'Replace long-lived implicit access tokens with short-lived access tokens after code exchange.'
                : 'Keep access tokens short-lived and never extend lifetime to compensate for client migration issues.',
            'refresh_token' => $input['refresh_token_needed'] === 'yes'
                ? 'Use refresh-token rotation with reuse detection and family revocation.'
                : 'Avoid refresh tokens; require re-authentication when short-lived access expires.',
            'rotation' => $input['refresh_token_needed'] === 'yes'
                ? 'Rotate on every refresh and revoke the token family when an old token reappears.'
                : 'No refresh-token rotation is needed when refresh tokens are not issued.',
            'review_note' => 'Support logout, client disablement, suspicious callback detection, and incident-response revocation without logging raw token values.',
        ];
    }

    /**
     * Return scope and consent checks for the OAuth migration review.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{scope_area: string, risk: string, review_rule: string}>
     */
    private function scopeConsentMatrixFor(array $input): array
    {
        return [
            [
                'scope_area' => 'openid profile',
                'risk' => 'Low identity scope, but callback validation, token audience, and redirect URI checks still apply.',
                'review_rule' => 'Allow only registered redirect URIs and keep ID-token handling out of URL fragments.',
            ],
            [
                'scope_area' => 'email',
                'risk' => 'Personal data scope that should be requested only when product behavior needs it.',
                'review_rule' => 'Show clear consent copy and avoid requesting email for pure sign-in when it is not needed.',
            ],
            [
                'scope_area' => 'offline_access',
                'risk' => $input['refresh_token_needed'] === 'yes'
                    ? 'High persistence scope because refresh tokens extend access beyond the browser session.'
                    : 'Avoid this scope when refresh tokens are not part of the design.',
                'review_rule' => $input['refresh_token_needed'] === 'yes'
                    ? 'Require refresh-token rotation, reuse detection, revocation, and audit logging.'
                    : 'Reject offline_access and use re-authentication instead.',
            ],
            [
                'scope_area' => 'admin or write scopes',
                'risk' => 'High-impact scopes can turn token leakage into account or data modification.',
                'review_rule' => 'Require least privilege, separate admin clients, short access lifetime, and explicit reviewer approval.',
            ],
        ];
    }

    /**
     * Return hardened authorize-request parameter rules.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{parameter: string, required_rule: string, reject_when: string}>
     */
    private function authorizeRequestHardeningFor(array $input): array
    {
        $rules = [
            [
                'parameter' => 'response_type',
                'required_rule' => 'Use response_type=code for every migrated client.',
                'reject_when' => 'Reject response_type=token, mixed token/code responses, or missing response_type.',
            ],
            [
                'parameter' => 'redirect_uri',
                'required_rule' => 'Match the exact registered redirect URI, including scheme, host, path, and trailing slash.',
                'reject_when' => 'Reject wildcard, unregistered, HTTP production, or drifted redirect URIs.',
            ],
            [
                'parameter' => 'state',
                'required_rule' => 'Generate high-entropy state per login attempt and bind it to the callback.',
                'reject_when' => 'Reject missing, reused, expired, or mismatched state values.',
            ],
            [
                'parameter' => 'scope',
                'required_rule' => 'Request the least privilege scope set needed for the current product action.',
                'reject_when' => 'Reject admin, write, or offline_access scopes unless the migration review approved them.',
            ],
        ];

        if ($input['can_keep_secret'] === 'no') {
            $rules[] = [
                'parameter' => 'code_challenge',
                'required_rule' => 'Public clients must send an S256 code_challenge derived from a high-entropy verifier.',
                'reject_when' => 'Reject missing code_challenge, code_challenge_method=plain, or low-entropy verifier evidence.',
            ];
        }

        return $rules;
    }

    /**
     * Return token-endpoint exchange rules for Authorization Code migration.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{field: string, rule: string, failure_response: string}>
     */
    private function tokenEndpointContractFor(array $input): array
    {
        $contract = [
            [
                'field' => 'grant_type',
                'rule' => 'Accept only authorization_code for the migrated login exchange.',
                'failure_response' => 'Return invalid_grant and log oauth_token_exchange_failed without raw credential values.',
            ],
            [
                'field' => 'code',
                'rule' => 'Exchange one unused, unexpired authorization code exactly once.',
                'failure_response' => 'Reject reused, expired, unknown, or already-consumed codes.',
            ],
            [
                'field' => 'redirect_uri',
                'rule' => 'Require the same redirect URI used in the authorize request.',
                'failure_response' => 'Reject redirect URI drift before issuing tokens.',
            ],
        ];

        if ($input['can_keep_secret'] === 'yes') {
            $contract[] = [
                'field' => 'client_secret',
                'rule' => 'Require backend-only client authentication for confidential clients.',
                'failure_response' => 'Reject missing or invalid client authentication and never expose the secret to JavaScript.',
            ];
        } else {
            $contract[] = [
                'field' => 'code_verifier',
                'rule' => 'Require a verifier that matches the original S256 code_challenge.',
                'failure_response' => 'Reject missing, wrong, replayed, or low-entropy verifier values.',
            ];
        }

        return $contract;
    }

    /**
     * Return frontend cleanup tasks needed after replacing Implicit callback handling.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{surface: string, cleanup_action: string, verification: string}>
     */
    private function frontendCleanupPlanFor(array $input): array
    {
        $plan = [
            [
                'surface' => 'callback route',
                'cleanup_action' => 'Delete access_token, id_token, token_type, and expires_in fragment parsing from the client callback.',
                'verification' => 'Callback tests fail if token-bearing URL input is accepted or parsed.',
            ],
            [
                'surface' => 'browser URL',
                'cleanup_action' => 'Clear callback query or fragment state after successful code handling with history.replaceState.',
                'verification' => 'Browser history and copied URL do not contain code, state, tokens, or verifier values after login completes.',
            ],
            [
                'surface' => 'client storage',
                'cleanup_action' => $input['current_flow'] === 'implicit'
                    ? 'Remove legacy access-token storage keys and migrate sessions through the Authorization Code path.'
                    : 'Keep token storage aligned with the selected Authorization Code architecture and avoid duplicate legacy keys.',
                'verification' => 'Automated smoke tests confirm no legacy token keys are written after migrated login.',
            ],
            [
                'surface' => 'analytics and error reporting',
                'cleanup_action' => 'Redact code, state, token-like fields, and redirect_uri values before analytics, support, or crash reporting.',
                'verification' => 'Log and telemetry tests prove OAuth credential-like values are masked before export.',
            ],
        ];

        if ($input['can_keep_secret'] === 'no') {
            $plan[] = [
                'surface' => 'PKCE verifier cache',
                'cleanup_action' => 'Clear the code_verifier after successful exchange, failed exchange, timeout, or user cancellation.',
                'verification' => 'Verifier lifecycle tests cover success, failure, timeout, cancellation, and tab concurrency.',
            ];
        }

        return $plan;
    }

    /**
     * Return ID-token validation rules for OIDC clients using the OAuth migration.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{claim: string, rule: string, reject_when: string}>
     */
    private function idTokenValidationRulesFor(array $input): array
    {
        return [
            [
                'claim' => 'iss',
                'rule' => 'Issuer must match the configured authorization server exactly.',
                'reject_when' => 'Reject tokens from unknown, staging, or lookalike issuers.',
            ],
            [
                'claim' => 'aud',
                'rule' => 'Audience must contain the expected OAuth client ID.',
                'reject_when' => 'Reject ID tokens issued for another client or API audience.',
            ],
            [
                'claim' => 'exp and iat',
                'rule' => 'Token timestamps must be valid with a small clock-skew allowance.',
                'reject_when' => 'Reject expired, future-issued, or unexpectedly old ID tokens.',
            ],
            [
                'claim' => 'nonce',
                'rule' => $input['can_keep_secret'] === 'no'
                    ? 'Public clients should bind nonce to the login attempt when an ID token is requested.'
                    : 'Backend clients should bind nonce or server-side session state to the login attempt when an ID token is requested.',
                'reject_when' => 'Reject missing, mismatched, expired, or replayed nonce values.',
            ],
        ];
    }

    /**
     * Return practical code examples for the safer flow.
     *
     * @return array<string, string>
     */
    private function implementationSnippets(): array
    {
        return [
            'implicit_legacy_url' => 'GET /authorize?response_type=token&client_id=spa&redirect_uri=https://app.example/callback&scope=openid profile',
            'pkce_authorize_url' => 'GET /authorize?response_type=code&client_id=spa&redirect_uri=https://app.example/callback&code_challenge=...&code_challenge_method=S256',
            'pkce_token_exchange' => "await fetch('/oauth/token', {\n  method: 'POST',\n  headers: { 'Content-Type': 'application/json' },\n  body: JSON.stringify({\n    grant_type: 'authorization_code',\n    code,\n    redirect_uri,\n    client_id,\n    code_verifier\n  })\n});",
            'client_credentials_token_request' => "POST /oauth/token\nAuthorization: Basic base64(client_id:client_secret)\nContent-Type: application/x-www-form-urlencoded\n\ngrant_type=client_credentials&scope=orders.read",
            'laravel_callback_shape' => "Route::get('/oauth/callback', function (Request \$request) {\n    \$request->validate(['code' => ['required', 'string'], 'state' => ['required', 'string']]);\n\n    // Exchange code server-side or through a PKCE-aware client.\n});",
        ];
    }

    /**
     * Return machine-to-machine checks when Client Credentials is selected.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array<int, array{area: string, check: string, evidence: string}>
     */
    private function clientCredentialsChecklistFor(array $input, array $recommendation): array
    {
        if ($recommendation['flow'] !== 'client-credentials') {
            return [
                [
                    'area' => 'flow fit',
                    'check' => 'Use Client Credentials only when the caller is a confidential backend service acting as itself.',
                    'evidence' => sprintf('Current client type is `%s`, so user-facing client flows should stay on Authorization Code variants.', $input['client_type']),
                ],
            ];
        }

        return [
            [
                'area' => 'client authentication',
                'check' => 'The token endpoint requires a confidential credential such as client secret, mTLS, or private key JWT.',
                'evidence' => 'Failed client authentication is logged without storing the raw secret.',
            ],
            [
                'area' => 'audience',
                'check' => 'Access tokens include the intended resource API audience and are rejected by other APIs.',
                'evidence' => 'Resource-server tests reject tokens with the wrong aud claim.',
            ],
            [
                'area' => 'scope',
                'check' => 'Scopes are service-level and least-privilege, such as orders.read or billing.write.',
                'evidence' => 'Missing or excessive scope is rejected before business logic runs.',
            ],
            [
                'area' => 'secret rotation',
                'check' => 'Client credentials have an owner, rotation schedule, revocation path, and emergency leak playbook.',
                'evidence' => 'Runbook names owner, rotation command, rollback, and audit event.',
            ],
            [
                'area' => 'no user delegation',
                'check' => 'The token is never treated as a user token and cannot access user-scoped permissions by accident.',
                'evidence' => 'Authorization policy distinguishes service subject from user subject.',
            ],
        ];
    }

    /**
     * Return the access boundary that a service token is allowed to cross.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array<int, array{resource: string, audience: string, allowed_scopes: array<int, string>, denied_by_default: array<int, string>, verification: string}>
     */
    private function clientCredentialsAccessContractFor(array $input, array $recommendation): array
    {
        if ($recommendation['flow'] !== 'client-credentials') {
            return [
                [
                    'resource' => 'user-facing OAuth client',
                    'audience' => $input['client_type'],
                    'allowed_scopes' => ['openid', 'profile'],
                    'denied_by_default' => ['client_credentials', 'service admin scopes'],
                    'verification' => 'Reject Client Credentials grants from public or user-facing clients.',
                ],
            ];
        }

        return [
            [
                'resource' => 'Orders API',
                'audience' => 'api://orders',
                'allowed_scopes' => ['orders.read', 'orders.write'],
                'denied_by_default' => ['billing.write', 'users.impersonate'],
                'verification' => 'Orders API accepts api://orders tokens with orders.* scopes and rejects every other audience.',
            ],
            [
                'resource' => 'Billing API',
                'audience' => 'api://billing',
                'allowed_scopes' => ['billing.read'],
                'denied_by_default' => ['billing.write', 'orders.write', 'users.impersonate'],
                'verification' => 'Billing API accepts read-only service scope unless a separate privileged client is approved.',
            ],
            [
                'resource' => 'User Profile API',
                'audience' => 'api://users',
                'allowed_scopes' => [],
                'denied_by_default' => ['users.read', 'users.write', 'users.impersonate'],
                'verification' => 'Service tokens cannot access user-scoped profile APIs without an explicit delegation design.',
            ],
        ];
    }

    /**
     * Return resource-server validation rules for service access tokens.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array<int, array{claim: string, rule: string, reject_when: string, evidence: string}>
     */
    private function clientCredentialsTokenValidationPolicyFor(array $input, array $recommendation): array
    {
        if ($recommendation['flow'] !== 'client-credentials') {
            return [
                [
                    'claim' => 'grant_type',
                    'rule' => sprintf('Do not accept Client Credentials tokens for `%s` user-facing sessions.', $input['client_type']),
                    'reject_when' => 'The token was minted through grant_type=client_credentials but the route expects a user subject.',
                    'evidence' => 'Authorization middleware separates service-token routes from user-session routes.',
                ],
            ];
        }

        return [
            [
                'claim' => 'iss',
                'rule' => 'Issuer must match the trusted authorization server for the environment.',
                'reject_when' => 'Issuer is missing, unknown, or from another environment.',
                'evidence' => 'Resource-server tests reject tokens signed by an untrusted issuer.',
            ],
            [
                'claim' => 'aud',
                'rule' => 'Audience must match the receiving API, such as api://orders.',
                'reject_when' => 'A token minted for Billing API is sent to Orders API or the audience is absent.',
                'evidence' => 'Wrong-audience tests fail before controller or business logic runs.',
            ],
            [
                'claim' => 'exp',
                'rule' => 'Token must be short-lived and not expired, with only small clock-skew tolerance.',
                'reject_when' => 'Token is expired, has no expiry, or exceeds the maximum service-token lifetime.',
                'evidence' => 'Expired-token tests return 401 and log oauth_service_token_expired.',
            ],
            [
                'claim' => 'scope',
                'rule' => 'Scope must include the exact service permission required by the route.',
                'reject_when' => 'Scope is missing, too broad for the route, or belongs to another resource domain.',
                'evidence' => 'Missing-scope and excessive-scope tests return 403 before business logic runs.',
            ],
            [
                'claim' => 'sub',
                'rule' => 'Subject must represent a service client, not a human user.',
                'reject_when' => 'Token has a user subject, impersonation scope, or ambiguous subject type.',
                'evidence' => 'Authorization policy branches on service subject versus user subject.',
            ],
        ];
    }

    /**
     * Return operational ownership guidance for service-to-service OAuth clients.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array{applies: bool, owner_model: string, rotation_model: string, audit_events: array<int, string>, incident_trigger: string}
     */
    private function clientCredentialsOperationalPlanFor(array $input, array $recommendation): array
    {
        if ($recommendation['flow'] !== 'client-credentials') {
            return [
                'applies' => false,
                'owner_model' => 'Use the selected OAuth client type ownership model instead of service-token ownership.',
                'rotation_model' => 'Follow Authorization Code or PKCE rollout controls.',
                'audit_events' => ['oauth_flow_selected'],
                'incident_trigger' => sprintf('Unexpected Client Credentials request from `%s` should be rejected and reviewed.', $input['client_type']),
            ];
        }

        return [
            'applies' => true,
            'owner_model' => 'Each OAuth client_id maps to one service owner, one runtime environment, and one resource audience.',
            'rotation_model' => 'Rotate client credentials on a fixed schedule, after team ownership changes, and immediately after suspected leakage.',
            'audit_events' => [
                'oauth_client_credentials_token_issued',
                'oauth_client_auth_failed',
                'oauth_service_scope_denied',
                'oauth_service_audience_denied',
                'oauth_client_secret_rotated',
            ],
            'incident_trigger' => 'Treat unexpected audience, excessive scope, repeated failed client authentication, or leaked client secret as a service-credential incident.',
        ];
    }

    /**
     * Return a rotation drill for client secrets used by machine-to-machine OAuth clients.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array<int, array{phase: string, action: string, verification: string}>
     */
    private function clientCredentialsRotationDrillFor(array $input, array $recommendation): array
    {
        if ($recommendation['flow'] !== 'client-credentials') {
            return [
                [
                    'phase' => 'not applicable',
                    'action' => sprintf('Do not issue client secrets to `%s`; keep this client on the selected user-facing OAuth flow.', $input['client_type']),
                    'verification' => 'Token endpoint rejects grant_type=client_credentials for this client_id.',
                ],
            ];
        }

        return [
            [
                'phase' => 'prepare',
                'action' => 'Create a second active credential for the same service owner, environment, and resource audience.',
                'verification' => 'New secret can request a scoped token while the old secret still works.',
            ],
            [
                'phase' => 'dual-secret rollout',
                'action' => 'Deploy the new secret through the runtime secret manager without logging the value or changing the client_id.',
                'verification' => 'Token issuance metrics show traffic moving from old credential fingerprint to new credential fingerprint.',
            ],
            [
                'phase' => 'revoke old secret',
                'action' => 'Disable the old credential after all healthy instances use the new secret.',
                'verification' => 'Old secret fails client authentication and the service still obtains tokens with the new secret.',
            ],
            [
                'phase' => 'audit',
                'action' => 'Record owner, reason, approver, rotated credential fingerprint, and affected audience.',
                'verification' => 'Audit log contains oauth_client_secret_rotated and no raw secret material.',
            ],
        ];
    }

    /**
     * Return production monitoring signals for Client Credentials service tokens.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array{applies: bool, metrics: array<int, string>, logs: array<int, string>, alerts: array<int, string>}
     */
    private function clientCredentialsMonitoringSignalsFor(array $input, array $recommendation): array
    {
        if ($recommendation['flow'] !== 'client-credentials') {
            return [
                'applies' => false,
                'metrics' => ['oauth_client_credentials_rejected_total by client_type and client_id'],
                'logs' => [sprintf('oauth_client_credentials_denied for `%s` without raw token or secret values', $input['client_type'])],
                'alerts' => ['Alert when a public or user-facing client repeatedly requests grant_type=client_credentials.'],
            ];
        }

        return [
            'applies' => true,
            'metrics' => [
                'oauth_client_credentials_token_issued_total by client_id, audience, scope_hash, and environment',
                'oauth_client_auth_failed_total by client_id, credential_fingerprint, and failure_reason',
                'oauth_service_scope_denied_total by client_id, resource, and required_scope',
                'oauth_service_audience_denied_total by client_id, token_audience, and resource_audience',
                'oauth_client_secret_rotation_age_days by client_id and owner_team',
            ],
            'logs' => [
                'oauth_client_credentials_token_issued with client_id, audience, scope_hash, token_ttl, and credential_fingerprint',
                'oauth_client_auth_failed with client_id, failure_reason, credential_fingerprint, and request_id',
                'oauth_service_token_rejected with client_id, resource, reject_reason, audience, and required_scope',
                'oauth_client_secret_rotated with client_id, owner_team, old_fingerprint, new_fingerprint, and approver',
            ],
            'alerts' => [
                'Alert when client-auth failures spike for one client_id or credential fingerprint.',
                'Alert when wrong-audience or missing-scope denials exceed the normal baseline.',
                'Alert when a client secret exceeds the maximum rotation age.',
                'Alert when token issuance for a service drops to zero during an active deployment.',
            ],
        ];
    }

    /**
     * Return incident handling guidance for common Client Credentials failures.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array<int, array{failure: string, trigger: string, action: string, owner: string}>
     */
    private function clientCredentialsFailurePlaybookFor(array $input, array $recommendation): array
    {
        if ($recommendation['flow'] !== 'client-credentials') {
            return [
                [
                    'failure' => 'wrong flow requested',
                    'trigger' => sprintf('`%s` requests grant_type=client_credentials.', $input['client_type']),
                    'action' => 'Reject the token request, keep the client on the selected Authorization Code variant, and notify the client owner.',
                    'owner' => 'auth platform owner',
                ],
            ];
        }

        return [
            [
                'failure' => 'client authentication failure',
                'trigger' => 'oauth_client_auth_failed spikes for one client_id or credential fingerprint.',
                'action' => 'Check recent secret rotation, deployment config, secret-manager version, and failed credential fingerprint before rotating again.',
                'owner' => 'service owner',
            ],
            [
                'failure' => 'wrong audience',
                'trigger' => 'oauth_service_audience_denied_total rises for a resource API.',
                'action' => 'Confirm the token audience requested by the caller, block cross-API reuse, and fix the client access contract.',
                'owner' => 'resource API owner',
            ],
            [
                'failure' => 'missing or excessive scope',
                'trigger' => 'oauth_service_scope_denied_total rises for one route or required_scope.',
                'action' => 'Map route permission to the minimum service scope and reject broad scopes such as users.impersonate.',
                'owner' => 'authorization policy owner',
            ],
            [
                'failure' => 'expired service token',
                'trigger' => 'oauth_service_token_expired appears after deploy or during long-running worker execution.',
                'action' => 'Refresh the token before expiry, bound token cache TTL below exp, and avoid sharing service tokens across workers.',
                'owner' => 'runtime service owner',
            ],
            [
                'failure' => 'suspected client secret leak',
                'trigger' => 'Unexpected token issuance from an unknown runtime, new IP range, or impossible deployment window.',
                'action' => 'Disable the credential, rotate secret, revoke active tokens if supported, and audit every resource audience used by that client.',
                'owner' => 'incident commander',
            ],
        ];
    }

    /**
     * Return migration steps away from Implicit.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, string>
     */
    private function migrationPlanFor(array $input, string $recommendedFlow): array
    {
        $steps = [
            'Inventory every redirect URI, OAuth client, scope, token lifetime, and frontend callback handler.',
            'Stop creating new clients that use response_type=token.',
            'Add state validation and PKCE code_verifier storage for public clients.',
            'Change authorization requests from response_type=token to response_type=code with code_challenge_method=S256.',
            'Exchange the authorization code at the token endpoint and reject callback URLs that contain access_token fragments.',
        ];

        if ($recommendedFlow === 'authorization-code-confidential-client') {
            $steps[] = 'Move token exchange into the backend and keep the client secret out of the browser.';
        }

        if ($input['refresh_token_needed'] === 'yes') {
            $steps[] = 'Use short-lived access tokens with refresh-token rotation and reuse detection.';
        }

        $steps[] = 'Add tests for state mismatch, missing PKCE verifier, reused code, expired code, and token leakage in URLs.';

        return $steps;
    }

    /**
     * Return an incremental rollout plan for replacing Implicit Flow.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array<int, array{phase: string, action: string, exit_signal: string}>
     */
    private function rolloutPlanFor(array $input, array $recommendation): array
    {
        return [
            [
                'phase' => 'inventory',
                'action' => 'List OAuth clients, redirect URIs, scopes, callback handlers, token lifetimes, and every response_type=token usage.',
                'exit_signal' => 'All legacy Implicit clients and callback paths are identified.',
            ],
            [
                'phase' => 'dual support',
                'action' => $recommendation['flow'] === 'block-until-pkce-supported'
                    ? 'Keep legacy clients blocked from production rollout while provider PKCE support is added.'
                    : 'Enable Authorization Code flow for selected clients while keeping legacy callbacks observable.',
                'exit_signal' => $input['pkce_supported'] === 'yes'
                    ? 'Pilot clients complete login with response_type=code and no token in callback URLs.'
                    : 'Provider or client library supports PKCE S256.',
            ],
            [
                'phase' => 'enforce',
                'action' => 'Reject response_type=token, token-bearing callback URLs, state mismatch, missing verifier, reused code, and expired code.',
                'exit_signal' => 'Security tests and callback telemetry show blocked legacy behavior.',
            ],
            [
                'phase' => 'remove legacy',
                'action' => 'Delete Implicit callback parsing, remove token-fragment handling, and revoke or shorten old access-token lifetimes.',
                'exit_signal' => 'No active client depends on Implicit Flow and logs show zero token-bearing callbacks.',
            ],
        ];
    }

    /**
     * Return a client-side cutover checklist for replacing Implicit callbacks.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array<int, array{item: string, owner: string, done_when: string}>
     */
    private function clientCutoverChecklistFor(array $input, array $recommendation): array
    {
        $items = [
            [
                'item' => 'replace authorize URL',
                'owner' => 'client developer',
                'done_when' => 'Client uses response_type=code and no longer requests response_type=token.',
            ],
            [
                'item' => 'store PKCE verifier safely',
                'owner' => 'client developer',
                'done_when' => $input['can_keep_secret'] === 'no'
                    ? 'code_verifier exists only for the login attempt and is cleared after callback.'
                    : 'Backend exchange path is used and client secret is never rendered to JavaScript.',
            ],
            [
                'item' => 'reject legacy token callback',
                'owner' => 'backend developer',
                'done_when' => 'Callback rejects access_token, id_token, token_type, and expires_in in URL input.',
            ],
            [
                'item' => 'verify recommended flow',
                'owner' => 'QA reviewer',
                'done_when' => "End-to-end login passes with {$recommendation['flow']} and callback logs show no token-bearing URL.",
            ],
        ];

        if ($input['refresh_token_needed'] === 'yes') {
            $items[] = [
                'item' => 'enable refresh rotation',
                'owner' => 'auth platform owner',
                'done_when' => 'Refresh-token family rotation and reuse detection are tested before broad rollout.',
            ];
        }

        return $items;
    }

    /**
     * Return telemetry needed while replacing Implicit Flow.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array{metrics: array<int, string>, log_events: array<int, string>, alerts: array<int, string>}
     */
    private function observabilityPlanFor(array $input): array
    {
        $metrics = [
            'oauth_authorize_request_total by client_id, response_type, and code_challenge_method',
            'oauth_callback_rejected_total by reason and client_id',
            'oauth_token_exchange_failed_total by grant_type, client_id, and failure_reason',
            'oauth_token_bearing_callback_total for callbacks containing access_token, id_token, token_type, or expires_in',
        ];

        if ($input['refresh_token_needed'] === 'yes') {
            $metrics[] = 'oauth_refresh_reuse_detected_total by client_id and token_family';
        }

        return [
            'metrics' => $metrics,
            'log_events' => [
                'oauth_authorize_started with client_id, response_type, redirect_uri_hash, scope_hash, and pkce_method',
                'oauth_callback_rejected with client_id, reason, state_status, and redirect_uri_hash',
                'oauth_code_exchanged with client_id, grant_type, pkce_verified, and token_family_id',
                'oauth_implicit_blocked with client_id, redirect_uri_hash, and detected_token_fields',
            ],
            'alerts' => [
                'Alert when response_type=token appears after the enforcement phase starts.',
                'Alert when token-bearing callback count is greater than zero after legacy removal.',
                'Alert when PKCE verifier failures spike for a migrated client.',
            ],
        ];
    }

    /**
     * Return common rollout failures and how to respond.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{failure: string, symptom: string, response: string}>
     */
    private function failureModesFor(array $input): array
    {
        $modes = [
            [
                'failure' => 'state mismatch',
                'symptom' => 'Callback is rejected even though the identity provider login succeeded.',
                'response' => 'Check state storage lifetime, same-site cookie behavior, tab concurrency, and replay protection.',
            ],
            [
                'failure' => 'missing PKCE verifier',
                'symptom' => 'Token endpoint rejects the authorization code exchange for public clients.',
                'response' => 'Persist the code_verifier only for the login attempt and clear it after a successful or failed exchange.',
            ],
            [
                'failure' => 'token-bearing callback',
                'symptom' => 'Callback receives access_token or id_token in URL input after migration starts.',
                'response' => 'Block the callback, log oauth_implicit_blocked, and update the client to response_type=code.',
            ],
            [
                'failure' => 'redirect URI drift',
                'symptom' => 'Authorization succeeds but token exchange fails with redirect_uri mismatch.',
                'response' => 'Use the exact registered redirect URI in both authorize and token exchange steps.',
            ],
        ];

        if ($input['refresh_token_needed'] === 'yes') {
            $modes[] = [
                'failure' => 'refresh-token reuse',
                'symptom' => 'A rotated refresh token is used again after a newer token was issued.',
                'response' => 'Revoke the refresh-token family, force re-authentication, and keep audit metadata without logging raw tokens.',
            ];
        }

        return $modes;
    }

    /**
     * Return incident response steps for token leakage during OAuth migration.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{phase: string, action: string, evidence: string}>
     */
    private function incidentResponsePlaybookFor(array $input): array
    {
        $steps = [
            [
                'phase' => 'triage',
                'action' => 'Confirm whether tokens appeared in callback URLs, logs, analytics, crash reports, screenshots, or support tickets.',
                'evidence' => 'client_id, redirect_uri_hash, detected_token_fields, timestamp, and request ID',
            ],
            [
                'phase' => 'contain',
                'action' => 'Block token-bearing callbacks, disable response_type=token for affected clients, and shorten active access-token lifetime.',
                'evidence' => 'client config change, blocked callback count, and affected client list',
            ],
            [
                'phase' => 'recover',
                'action' => $input['refresh_token_needed'] === 'yes'
                    ? 'Rotate or revoke affected refresh-token families and force re-authentication for risky sessions.'
                    : 'Force re-authentication for affected sessions after access-token expiry.',
                'evidence' => 'token family IDs, session IDs, revocation records, and user notification decision',
            ],
            [
                'phase' => 'harden',
                'action' => 'Add or tighten tests for token-bearing callbacks, state mismatch, missing verifier, and redirect URI drift.',
                'evidence' => 'new failing tests, fixed test result, and updated deprecation policy',
            ],
        ];

        return $steps;
    }

    /**
     * Return rules for retiring Implicit Flow safely.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{rule: string, applies_to: string, enforcement: string}>
     */
    private function deprecationPolicyFor(array $input): array
    {
        return [
            [
                'rule' => 'no new implicit clients',
                'applies_to' => 'all OAuth clients',
                'enforcement' => 'Reject new client registrations or config changes that use response_type=token.',
            ],
            [
                'rule' => 'legacy client exception requires owner',
                'applies_to' => $input['current_flow'] === 'implicit' ? 'current legacy client' : 'legacy clients only',
                'enforcement' => 'Require named owner, migration date, redirect URI inventory, and observability before any grace period.',
            ],
            [
                'rule' => 'public clients require PKCE',
                'applies_to' => 'browser, mobile, and other public clients',
                'enforcement' => 'Block production login unless response_type=code, code_challenge_method=S256, and state validation are present.',
            ],
            [
                'rule' => 'token-bearing callback is a security event',
                'applies_to' => 'all callback endpoints',
                'enforcement' => 'Reject the callback, emit oauth_implicit_blocked, and alert during enforcement or removal phases.',
            ],
        ];
    }

    /**
     * Return review checks before accepting an OAuth flow.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{area: string, question: string, pass_signal: string}>
     */
    private function reviewChecklistFor(array $input): array
    {
        return [
            [
                'area' => 'flow choice',
                'question' => 'Does the client use Authorization Code with PKCE instead of Implicit when it cannot keep a secret?',
                'pass_signal' => 'Authorize URL uses response_type=code and code_challenge_method=S256.',
            ],
            [
                'area' => 'token exposure',
                'question' => 'Can access tokens appear in callback URLs, browser history, logs, referrers, or screenshots?',
                'pass_signal' => 'Callbacks reject access_token fragments and logs redact token-like values.',
            ],
            [
                'area' => 'state and PKCE',
                'question' => 'Are state and code_verifier validated before token exchange?',
                'pass_signal' => 'Tests cover state mismatch and missing or wrong code_verifier.',
            ],
            [
                'area' => 'refresh strategy',
                'question' => $input['refresh_token_needed'] === 'yes'
                    ? 'Are refresh tokens rotated and revoked on reuse?'
                    : 'Is re-authentication acceptable when the access token expires?',
                'pass_signal' => 'Expiry, refresh, and reuse behavior are covered by tests.',
            ],
        ];
    }

    /**
     * Return verification tests for the selected flow.
     *
     * @return array<int, string>
     */
    private function testsFor(string $recommendedFlow): array
    {
        if ($recommendedFlow === 'client-credentials') {
            return [
                'Token endpoint requires confidential client authentication.',
                'Client Credentials token contains service scopes and expected audience.',
                'Resource server rejects service token with missing scope or wrong audience.',
                'Browser and mobile clients cannot request Client Credentials tokens.',
                'Client secret rotation and failed-auth attempts are logged without exposing the secret.',
            ];
        }

        $tests = [
            'Authorization callback rejects access_token in the URL fragment or query.',
            'Authorization request uses response_type=code.',
            'PKCE code_verifier is required for public clients.',
            'State mismatch is rejected before token exchange.',
            'Expired or reused authorization codes are rejected.',
        ];

        if ($recommendedFlow === 'authorization-code-confidential-client') {
            $tests[] = 'Client secret is used only from the backend and never rendered to JavaScript.';
        }

        return $tests;
    }

    /**
     * Return concrete security tests for OAuth flow migration.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @return array<int, array{category: string, scenario: string, expected_result: string}>
     */
    private function securityTestMatrixFor(array $input): array
    {
        $matrix = [
            [
                'category' => 'front-channel token blocking',
                'scenario' => 'Callback URL includes access_token or id_token after migration begins.',
                'expected_result' => 'Request is rejected and oauth_implicit_blocked is logged without storing raw token values.',
            ],
            [
                'category' => 'state validation',
                'scenario' => 'Callback state is missing, mismatched, expired, or replayed.',
                'expected_result' => 'Token exchange does not run and oauth_callback_rejected records state_status.',
            ],
            [
                'category' => 'PKCE verifier',
                'scenario' => 'Public client exchanges code with missing or wrong code_verifier.',
                'expected_result' => 'Token endpoint rejects the exchange and increments oauth_token_exchange_failed_total.',
            ],
            [
                'category' => 'authorization code lifecycle',
                'scenario' => 'Authorization code is reused or exchanged after expiry.',
                'expected_result' => 'Exchange fails and the attempt is logged as reused_code or expired_code.',
            ],
        ];

        if ($input['refresh_token_needed'] === 'yes') {
            $matrix[] = [
                'category' => 'refresh-token rotation',
                'scenario' => 'Old refresh token is used after a new token was issued.',
                'expected_result' => 'Refresh-token family is revoked and oauth_refresh_reuse_detected_total increments.',
            ];
        }

        if ($input['client_type'] === 'service-to-service') {
            $matrix[] = [
                'category' => 'client authentication',
                'scenario' => 'Service requests a token with missing, wrong, expired, or rotated client secret.',
                'expected_result' => 'Token endpoint rejects the request and logs oauth_client_auth_failed without storing the raw secret.',
            ];
            $matrix[] = [
                'category' => 'audience and scope',
                'scenario' => 'Service token is sent to the wrong API audience or lacks the required service scope.',
                'expected_result' => 'Resource server rejects the call before business logic runs.',
            ];
        }

        return $matrix;
    }

    /**
     * Return interview checklist points for Client Credentials discussions.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array<int, array{point: string, answer_signal: string, red_flag: string}>
     */
    private function clientCredentialsInterviewChecklistFor(array $input, array $recommendation): array
    {
        if ($recommendation['flow'] !== 'client-credentials') {
            return [
                [
                    'point' => 'flow fit',
                    'answer_signal' => sprintf('Explain why `%s` should use an Authorization Code variant instead of Client Credentials.', $input['client_type']),
                    'red_flag' => 'Treating a browser, mobile, or user-facing app as if it can safely keep a client secret.',
                ],
            ];
        }

        return [
            [
                'point' => 'machine-to-machine only',
                'answer_signal' => 'State clearly that Client Credentials is for a backend service acting as itself without user login.',
                'red_flag' => 'Mentioning user consent, browser redirect, or PKCE as the main mechanism for this flow.',
            ],
            [
                'point' => 'confidential client authentication',
                'answer_signal' => 'Name client secret, mTLS, or private key JWT and explain why the credential must stay server-side.',
                'red_flag' => 'Putting client_secret in SPA, mobile app, JavaScript, or user device storage.',
            ],
            [
                'point' => 'audience and scope boundary',
                'answer_signal' => 'Explain that resource servers validate audience and exact service scopes before business logic.',
                'red_flag' => 'Using one broad token for every internal API or skipping aud validation.',
            ],
            [
                'point' => 'no user delegation',
                'answer_signal' => 'Call out that the service token is not a user token and should not impersonate users by default.',
                'red_flag' => 'Using Client Credentials to access user profile data without an explicit delegation design.',
            ],
            [
                'point' => 'operations',
                'answer_signal' => 'Mention secret rotation, audit logs, auth-failure monitoring, and incident response for leaked credentials.',
                'red_flag' => 'Describing only the happy-path token request and ignoring rotation or failed authentication.',
            ],
        ];
    }

    /**
     * Return a production-readiness rubric for Client Credentials designs.
     *
     * @param  array{client_type: string, current_flow: string, can_keep_secret: string, pkce_supported: string, refresh_token_needed: string, browser_history_risk: string}  $input
     * @param  array{flow: string, label: string, reason: string, migration_needed: bool}  $recommendation
     * @return array<int, array{criterion: string, weight: int, pass_signal: string, fail_signal: string}>
     */
    private function clientCredentialsSecurityReviewRubricFor(array $input, array $recommendation): array
    {
        if ($recommendation['flow'] !== 'client-credentials') {
            return [
                [
                    'criterion' => 'flow eligibility',
                    'weight' => 100,
                    'pass_signal' => sprintf('`%s` remains on the selected Authorization Code variant.', $input['client_type']),
                    'fail_signal' => 'Client Credentials is allowed for a public or user-facing client.',
                ],
            ];
        }

        return [
            [
                'criterion' => 'confidential credential boundary',
                'weight' => 25,
                'pass_signal' => 'Credential is stored only in backend secret management and never appears in browser, mobile, logs, or artifacts.',
                'fail_signal' => 'client_secret is copied into frontend code, mobile storage, screenshots, CI logs, or support tickets.',
            ],
            [
                'criterion' => 'audience and scope enforcement',
                'weight' => 25,
                'pass_signal' => 'Every resource API validates aud and exact service scope before controller or business logic runs.',
                'fail_signal' => 'One broad token works across unrelated APIs or missing scopes reach business logic.',
            ],
            [
                'criterion' => 'token validation',
                'weight' => 20,
                'pass_signal' => 'Resource server validates issuer, signature, expiry, audience, subject type, and required scope.',
                'fail_signal' => 'API trusts token presence only or accepts expired, wrong-issuer, wrong-audience, or user-subject tokens.',
            ],
            [
                'criterion' => 'rotation and revocation',
                'weight' => 15,
                'pass_signal' => 'Dual-secret rotation, old-secret revocation, owner, approver, and audit event are documented and tested.',
                'fail_signal' => 'No owner, no rotation schedule, no rollback path, or no evidence after secret rotation.',
            ],
            [
                'criterion' => 'monitoring and incident response',
                'weight' => 15,
                'pass_signal' => 'Auth failures, audience denials, scope denials, token issuance, and rotation age emit metrics, logs, and alerts.',
                'fail_signal' => 'The team can only debug failures manually after users or downstream services report breakage.',
            ],
        ];
    }

    /**
     * Return an interview-ready OAuth answer.
     */
    private function interviewAnswerFor(array $input, string $recommendedFlow): string
    {
        if ($recommendedFlow === 'client-credentials') {
            return 'Client Credentials Flow is OAuth2 for machine-to-machine access. There is no user login: a confidential backend service authenticates to the token endpoint as itself, receives a scoped access token, and the resource server validates issuer, audience, expiry, signature, and scopes. I would use it for internal service calls, workers, scheduled jobs, and microservice APIs, but not for SPAs or mobile apps because they cannot keep a client secret.';
        }

        return "Implicit Flow is no longer recommended because it returns access tokens through the browser front channel, where tokens can leak through URLs, redirects, history, extensions, logs, or screenshots. For {$input['client_type']}, I would use {$recommendedFlow}: Authorization Code with PKCE for public clients, or Authorization Code with a confidential backend when the server can keep a secret. The key difference is that the browser gets a short-lived code, not the token, and PKCE proves the token exchange came from the original client.";
    }
}
