<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class SystemDesignTradeoffPlanService
{
    /**
     * Build a system-design interview plan from ambiguity, constraints, and tradeoffs.
     *
     * @param  array{scenario: string, latency_requirement: string, failure_impact: string, consistency_need: string, team_maturity: string, operational_capacity: string, current_constraint: string, target_level?: string, candidate_answer?: string|null}  $input
     * @return array<string, mixed>
     */
    public function plan(array $input): array
    {
        $scores = $this->scoreOptions($input);
        $recommendation = $this->recommendationFor($scores);

        return [
            'recommendation' => $recommendation,
            'score_breakdown' => $scores,
            'clarifying_questions' => $this->clarifyingQuestionsFor($input),
            'decision_matrix' => $this->decisionMatrixFor($input),
            'decision_tree' => $this->decisionTreeFor($input, $recommendation['style']),
            'tradeoff_statement' => $this->tradeoffStatementFor($input, $recommendation),
            'three_question_check' => $this->threeQuestionCheckFor($input),
            'answer_scorecard' => $this->answerScorecardFor($input, $recommendation['style']),
            'anti_patterns' => $this->antiPatternsFor($recommendation['style']),
            'green_flags' => $this->greenFlagsFor($recommendation['style']),
            'architecture_plan' => $this->architecturePlanFor($input, $recommendation['style']),
            'architecture_evolution_path' => $this->architectureEvolutionPathFor($input, $recommendation['style']),
            'operating_model' => $this->operatingModelFor($input, $recommendation['style']),
            'risk_register' => $this->riskRegisterFor($input, $recommendation['style']),
            'decision_review_triggers' => $this->decisionReviewTriggersFor($recommendation['style']),
            'interviewer_followups' => $this->interviewerFollowupsFor($recommendation['style']),
            'interviewer_simulation' => $this->interviewerSimulationFor($input, $recommendation),
            'rewrite_practice' => $this->rewritePracticeFor($recommendation),
            'answer_contract' => $this->answerContractFor($input, $recommendation),
            'review_checklist' => $this->reviewChecklistFor($recommendation['style']),
            'practice_prompt' => $this->practicePromptFor($input),
            'calibration_examples' => $this->calibrationExamplesFor($recommendation['style']),
            'one_minute_answer' => $this->timedAnswerFor($input, $recommendation, 'one-minute'),
            'two_minute_answer' => $this->timedAnswerFor($input, $recommendation, 'two-minute'),
            'drill_cards' => $this->drillCardsFor($recommendation['style']),
            'scenario_variations' => $this->scenarioVariationsFor($input),
            'score_interpretation' => $this->scoreInterpretationFor($this->answerScorecardFor($input, $recommendation['style'])),
            'level_framing' => $this->levelFramingFor($recommendation['label']),
            'target_level_plan' => $this->targetLevelPlanFor($input['target_level'] ?? 'l6', $recommendation['label']),
            'candidate_answer_review' => $this->candidateAnswerReviewFor($input['candidate_answer'] ?? null),
            'interview_answer' => $this->interviewAnswerFor($input, $recommendation),
            'decision_memo_markdown' => $this->decisionMemoFor($input, $recommendation, $scores),
            'interview_packet_markdown' => $this->interviewPacketMarkdownFor($input, $recommendation),
            'commands' => [
                'php artisan test --filter SystemDesignTradeoffPlan',
                'php artisan route:list --path=system-design-tradeoff-plan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Score implementation options from the stated constraints.
     *
     * @param  array{scenario: string, latency_requirement: string, failure_impact: string, consistency_need: string, team_maturity: string, operational_capacity: string, current_constraint: string, target_level?: string, candidate_answer?: string|null}  $input
     * @return array{long_polling: int, websocket_event_stream: int, event_driven_broker: int, vertical_scale_first: int, strong_consistency_first: int, signals: array<int, array{signal: string, note: string}>}
     */
    private function scoreOptions(array $input): array
    {
        $scores = [
            'long_polling' => 0,
            'websocket_event_stream' => 0,
            'event_driven_broker' => 0,
            'vertical_scale_first' => 0,
            'strong_consistency_first' => 0,
        ];
        $signals = [];

        if ($input['latency_requirement'] === 'real-time') {
            $scores['websocket_event_stream'] += 8;
            $scores['event_driven_broker'] += 2;
            $signals[] = ['signal' => 'latency', 'note' => 'Real-time delivery pushes the design toward WebSocket or another push channel.'];
        }

        if ($input['latency_requirement'] === 'delayed') {
            $scores['long_polling'] += 4;
            $scores['event_driven_broker'] += 1;
            $signals[] = ['signal' => 'latency', 'note' => 'Delay tolerance makes simpler HTTP polling or batch delivery viable.'];
        }

        if ($input['failure_impact'] === 'high') {
            $scores['event_driven_broker'] += 2;
            $scores['strong_consistency_first'] += 2;
            $signals[] = ['signal' => 'failure impact', 'note' => 'High business impact requires redundancy, replay, and explicit failover cost.'];
        }

        if ($input['consistency_need'] === 'strong') {
            $scores['strong_consistency_first'] += 5;
            $signals[] = ['signal' => 'consistency', 'note' => 'Strong consistency is worth paying for when duplicate or stale actions hurt users or money.'];
        } else {
            $scores['event_driven_broker'] += 1;
            $scores['long_polling'] += 1;
            $signals[] = ['signal' => 'consistency', 'note' => 'Eventual consistency is acceptable when delay or duplicate notification is not catastrophic.'];
        }

        if ($input['operational_capacity'] === 'low') {
            $scores['long_polling'] += 3;
            $scores['vertical_scale_first'] += 2;
            $signals[] = ['signal' => 'operations', 'note' => 'Low operational capacity should penalize designs that need sticky sessions, brokers, and many moving parts.'];
        }

        if ($input['team_maturity'] === 'platform') {
            $scores['websocket_event_stream'] += 2;
            $scores['event_driven_broker'] += 2;
            $signals[] = ['signal' => 'team', 'note' => 'A platform team can carry more operational complexity when the business value justifies it.'];
        }

        if ($input['current_constraint'] === 'legacy-database') {
            $scores['vertical_scale_first'] += 5;
            $signals[] = ['signal' => 'legacy', 'note' => 'A hard legacy database can make vertical scaling or incremental wrapping safer than a rewrite.'];
        }

        if ($input['scenario'] === 'payment-flow') {
            $scores['strong_consistency_first'] += 4;
        }

        if ($input['scenario'] === 'notification-10m') {
            $scores['event_driven_broker'] += 2;
        }

        if ($input['scenario'] === 'startup-microservices' || $input['team_maturity'] === 'small') {
            $scores['long_polling'] += 2;
            $scores['vertical_scale_first'] += 1;
        }

        return [...$scores, 'signals' => $signals];
    }

    /**
     * Pick the highest-scoring option.
     *
     * @param  array{long_polling: int, websocket_event_stream: int, event_driven_broker: int, vertical_scale_first: int, strong_consistency_first: int, signals: array<int, array{signal: string, note: string}>}  $scores
     * @return array{style: string, label: string, reason: string}
     */
    private function recommendationFor(array $scores): array
    {
        $numericScores = $scores;
        unset($numericScores['signals']);
        arsort($numericScores);
        $style = (string) array_key_first($numericScores);

        return match ($style) {
            'websocket_event_stream' => [
                'style' => $style,
                'label' => 'Use WebSocket push backed by an event stream',
                'reason' => 'The design needs low-latency delivery enough to justify connection management, fan-out, sticky-session avoidance, and broker operations.',
            ],
            'event_driven_broker' => [
                'style' => $style,
                'label' => 'Use an event-driven broker as the notification backbone',
                'reason' => 'The system needs decoupling, replay, buffering, and fan-out more than a simple synchronous request path.',
            ],
            'vertical_scale_first' => [
                'style' => $style,
                'label' => 'Scale vertically or incrementally before splitting the system',
                'reason' => 'The current constraints make a large distributed redesign more expensive than buying time with a smaller deployable change.',
            ],
            'strong_consistency_first' => [
                'style' => $style,
                'label' => 'Prioritize strong consistency and idempotency before throughput',
                'reason' => 'Correctness failure is more expensive than latency or infrastructure cost in this scenario.',
            ],
            default => [
                'style' => 'long_polling',
                'label' => 'Start with Long Polling or simple HTTP delivery',
                'reason' => 'The business can tolerate delay, and the simpler operational model is cheaper for the current team.',
            ],
        };
    }

    /**
     * Questions to ask before drawing boxes.
     *
     * @return array<int, string>
     */
    private function clarifyingQuestionsFor(array $input): array
    {
        return [
            'Does notification delivery need to be real-time, near-real-time, or can it be delayed by 30 seconds?',
            'Which clients must receive it: web, mobile, email, push, or internal dashboard?',
            'If this system is unavailable for five minutes, what business or user harm happens?',
            'Is duplicate delivery acceptable, or does the domain require strong consistency and idempotency?',
            sprintf('Can the current %s team operate the design after launch, not only build it?', $input['team_maturity']),
        ];
    }

    /**
     * Compare common choices with their cost.
     *
     * @return array<int, array{option: string, best_when: string, cost: string, avoid_when: string}>
     */
    private function decisionMatrixFor(array $input): array
    {
        return [
            [
                'option' => 'Long Polling',
                'best_when' => 'Delay is acceptable and the team wants simple HTTP operations.',
                'cost' => 'More repeated requests and less elegant code flow.',
                'avoid_when' => 'The product requires low-latency two-way presence or live collaboration.',
            ],
            [
                'option' => 'WebSocket push',
                'best_when' => 'Users need live updates and the team can operate many open connections.',
                'cost' => 'Connection state, load balancer behavior, fan-out, backpressure, and monitoring become harder.',
                'avoid_when' => $input['operational_capacity'] === 'low' ? 'Operations capacity is low.' : 'The value of real-time delivery is unclear.',
            ],
            [
                'option' => 'Kafka or event broker',
                'best_when' => 'High-throughput fan-out, replay, buffering, and decoupling are real requirements.',
                'cost' => 'Schema governance, consumer lag, partitioning, retries, and on-call complexity.',
                'avoid_when' => 'The system only needs a simple request-response flow.',
            ],
            [
                'option' => 'Vertical scaling first',
                'best_when' => 'A legacy dependency or deadline makes distributed redesign too risky.',
                'cost' => 'It buys time but does not remove the underlying bottleneck forever.',
                'avoid_when' => 'The bottleneck is already distributed traffic routing rather than one constrained machine.',
            ],
            [
                'option' => 'Strong consistency and idempotency first',
                'best_when' => 'Wrong or duplicate actions harm money, permissions, orders, or compliance.',
                'cost' => 'Lower throughput, more locking/state checks, and more careful failure handling.',
                'avoid_when' => 'The domain only sends low-risk notifications where eventual consistency is acceptable.',
            ],
        ];
    }

    /**
     * Explain the decision path in plain branches.
     *
     * @return array<int, array{question: string, current_answer: string, implication: string}>
     */
    private function decisionTreeFor(array $input, string $style): array
    {
        return [
            [
                'question' => 'Is real-time delivery a hard requirement?',
                'current_answer' => $input['latency_requirement'],
                'implication' => $input['latency_requirement'] === 'real-time'
                    ? 'Push delivery is worth considering, but it must include connection ownership and fan-out.'
                    : 'Polling, batching, or queue-backed delivery can be cheaper than always-on connections.',
            ],
            [
                'question' => 'Does wrong or duplicate delivery harm money, permissions, or compliance?',
                'current_answer' => $input['consistency_need'],
                'implication' => $input['consistency_need'] === 'strong'
                    ? 'Correctness and idempotency should be designed before throughput.'
                    : 'Eventual consistency is acceptable if the user/business cost of delay is low.',
            ],
            [
                'question' => 'Can the team operate the chosen design?',
                'current_answer' => $input['team_maturity'].' team with '.$input['operational_capacity'].' operational capacity',
                'implication' => $input['operational_capacity'] === 'low'
                    ? 'Prefer a smaller deployable design unless the business harm forces complexity.'
                    : 'Advanced infrastructure is possible if ownership, metrics, and rollback are explicit.',
            ],
            [
                'question' => 'What does the current branch recommend?',
                'current_answer' => $style,
                'implication' => 'Use this as a provisional decision, then name the metric that can reverse it.',
            ],
        ];
    }

    /**
     * Return a concise tradeoff sentence for interviews.
     *
     * @param  array{style: string, label: string, reason: string}  $recommendation
     */
    private function tradeoffStatementFor(array $input, array $recommendation): string
    {
        $cost = match ($recommendation['style']) {
            'websocket_event_stream' => 'higher operational complexity for connection state, fan-out, and monitoring',
            'event_driven_broker' => 'broker operations, schema governance, retries, and consumer lag',
            'vertical_scale_first' => 'limited long-term scalability and a planned revisit point',
            'strong_consistency_first' => 'lower throughput and more stateful checks',
            default => 'less immediate delivery and more polling traffic',
        };

        $rejectedCost = $input['operational_capacity'] === 'low'
            ? 'operating a distributed design the team cannot safely support'
            : 'paying complexity before the business constraint proves it is needed';

        return sprintf('I choose %s. The price is %s. I accept that because, in this context, %s is more expensive.', $recommendation['label'], $cost, $rejectedCost);
    }

    /**
     * Encode the three senior questions from the article.
     *
     * @return array<int, array{question: string, answer: string}>
     */
    private function threeQuestionCheckFor(array $input): array
    {
        return [
            [
                'question' => 'If this is wrong, who pays the cost?',
                'answer' => $input['failure_impact'] === 'high'
                    ? 'Users or the business pay directly, so failover, correctness, replay, and communication matter.'
                    : 'The blast radius is smaller, so simplicity can beat perfect infrastructure.',
            ],
            [
                'question' => 'Where does the complexity live?',
                'answer' => $input['latency_requirement'] === 'real-time'
                    ? 'The code may look simple, but operations carry connection state, broker fan-out, and backpressure.'
                    : 'The code may be less elegant, but operations stay closer to normal HTTP behavior.',
            ],
            [
                'question' => 'Can this team carry it after launch?',
                'answer' => $input['team_maturity'] === 'small'
                    ? 'A small team should avoid designs that require platform-level ownership unless the business impact demands it.'
                    : 'The team can consider a more advanced design if SLOs, ownership, and runbooks are explicit.',
            ],
        ];
    }

    /**
     * Provide a scoring rubric for self-reviewing an interview answer.
     *
     * @return array{max_score: int, passing_score: int, dimensions: array<int, array{name: string, points: int, evidence: string}>}
     */
    private function answerScorecardFor(array $input, string $style): array
    {
        return [
            'max_score' => 20,
            'passing_score' => $input['scenario'] === 'payment-flow' ? 17 : 15,
            'dimensions' => [
                [
                    'name' => 'Clarifies constraints before drawing',
                    'points' => 4,
                    'evidence' => 'Mentions latency, platforms, outage impact, duplicate tolerance, and team capacity.',
                ],
                [
                    'name' => 'States the accepted tradeoff',
                    'points' => 4,
                    'evidence' => 'Uses the pattern: I choose A; the cost is X; I accept it because Y costs more here.',
                ],
                [
                    'name' => 'Compares a rejected alternative',
                    'points' => 3,
                    'evidence' => match ($style) {
                        'websocket_event_stream' => 'Explains why polling is too slow or why the real-time value pays for WebSocket operations.',
                        'event_driven_broker' => 'Explains why synchronous delivery is too coupled or too hard to replay.',
                        'strong_consistency_first' => 'Explains why eventual consistency is unsafe for money or correctness.',
                        'vertical_scale_first' => 'Explains why distributed redesign is too risky for the current constraint.',
                        default => 'Explains why push infrastructure is not worth its operational cost yet.',
                    },
                ],
                [
                    'name' => 'Names operational ownership',
                    'points' => 3,
                    'evidence' => 'Names who owns metrics, runbook, rollback, and on-call behavior after launch.',
                ],
                [
                    'name' => 'Defines a review trigger',
                    'points' => 3,
                    'evidence' => 'States the metric or condition that would make the team revisit the design.',
                ],
                [
                    'name' => 'Frames the answer at the right level',
                    'points' => 3,
                    'evidence' => 'Moves beyond naming tools and ties the decision to business impact and organization maturity.',
                ],
            ],
        ];
    }

    /**
     * Return weak answer patterns to avoid.
     *
     * @return array<int, array{pattern: string, why_it_fails: string, better_move: string}>
     */
    private function antiPatternsFor(string $style): array
    {
        return [
            [
                'pattern' => 'Tool-first answer',
                'why_it_fails' => 'It sounds memorized because Kafka, WebSocket, Redis, and Kubernetes are named before constraints.',
                'better_move' => 'Ask the clarifying questions first, then choose the tool only if the answers justify it.',
            ],
            [
                'pattern' => 'No rejected alternative',
                'why_it_fails' => 'The interviewer cannot see judgment if every option appears free.',
                'better_move' => 'Compare the chosen design against one simpler and one more powerful alternative.',
            ],
            [
                'pattern' => 'Paper architecture',
                'why_it_fails' => 'A diagram can be correct but still impossible for the team to operate.',
                'better_move' => 'Name operational ownership, metrics, runbook, and rollback.',
            ],
            [
                'pattern' => match ($style) {
                    'websocket_event_stream' => 'WebSocket without fan-out plan',
                    'event_driven_broker' => 'Broker without retry and lag plan',
                    'strong_consistency_first' => 'Strong consistency without idempotency',
                    'vertical_scale_first' => 'Interim scaling without a review trigger',
                    default => 'Polling without a cost ceiling',
                },
                'why_it_fails' => 'The answer hides the hardest failure mode of the chosen approach.',
                'better_move' => 'Expose the failure mode and state the first metric that catches it.',
            ],
        ];
    }

    /**
     * Return strong answer signals the learner should aim for.
     *
     * @return array<int, string>
     */
    private function greenFlagsFor(string $style): array
    {
        return [
            'Asks requirements that can materially change the architecture.',
            'Explains one chosen cost and one rejected cost.',
            'Names who is harmed if the design is wrong.',
            'Separates code complexity from operational complexity.',
            'Checks whether the team can run the design after launch.',
            match ($style) {
                'websocket_event_stream' => 'Mentions connection ownership, fan-out path, and backpressure.',
                'event_driven_broker' => 'Mentions replay, consumer lag, idempotent delivery, and schema ownership.',
                'strong_consistency_first' => 'Mentions idempotency, transactions, unique constraints, and reconciliation.',
                'vertical_scale_first' => 'Mentions an interim decision, capacity headroom, and redesign trigger.',
                default => 'Mentions delay budget, polling cost, and when push would become justified.',
            },
        ];
    }

    /**
     * Provide a high-level architecture plan.
     *
     * @return array<string, mixed>
     */
    private function architecturePlanFor(array $input, string $style): array
    {
        return [
            'entrypoint' => 'Start with clarifying requirements before drawing architecture boxes.',
            'primary_path' => match ($style) {
                'websocket_event_stream' => 'API writes notification events, broker partitions by user or tenant, WebSocket gateway fans out to connected clients, and offline users fall back to durable storage or push.',
                'event_driven_broker' => 'API records intent, publishes an event, workers fan out per channel, retries are idempotent, and delivery state is observable.',
                'vertical_scale_first' => 'Keep the current system shape, raise capacity or isolate the bottleneck, add measurement, and define the trigger for a future split.',
                'strong_consistency_first' => 'Protect the write path with idempotency keys, unique business constraints, transactions, and reconciliation before optimizing throughput.',
                default => 'API stores notification state, clients poll or long-poll, responses are cache-aware, and background jobs handle slow channels.',
            },
            'operational_owner' => $input['team_maturity'] === 'platform' ? 'platform team with service SLOs' : 'feature team with a narrow runbook',
            'review_gate' => 'State the accepted cost, the rejected alternative, rollback plan, and metric that would change the decision.',
        ];
    }

    /**
     * Explain how the architecture can evolve without pretending the first design is final.
     *
     * @return array<int, array{phase: string, decision: string, exit_criteria: string}>
     */
    private function architectureEvolutionPathFor(array $input, string $style): array
    {
        return [
            [
                'phase' => 'Phase 1: smallest safe design',
                'decision' => match ($style) {
                    'websocket_event_stream' => 'Start with one gateway path, external fan-out, and delivery metrics before expanding channels.',
                    'event_driven_broker' => 'Start with a durable event backbone and one idempotent consumer group.',
                    'strong_consistency_first' => 'Start with idempotent writes, unique constraints, transaction boundaries, and reconciliation logs.',
                    'vertical_scale_first' => 'Start by buying safe capacity and adding measurement around the legacy bottleneck.',
                    default => 'Start with simple HTTP delivery and a clear delay budget.',
                },
                'exit_criteria' => 'Focused metrics prove whether the selected cost is still acceptable.',
            ],
            [
                'phase' => 'Phase 2: harden the chosen bottleneck',
                'decision' => match ($style) {
                    'websocket_event_stream' => 'Add connection draining, regional gateway health, backpressure, and offline fallback.',
                    'event_driven_broker' => 'Add schema compatibility checks, dead-letter handling, and replay tooling.',
                    'strong_consistency_first' => 'Add reconciliation jobs, audit trails, and duplicate-attempt dashboards.',
                    'vertical_scale_first' => 'Add tests and ownership around the legacy boundary so a later split is safer.',
                    default => 'Add cache headers, client retry limits, and polling cost dashboards.',
                },
                'exit_criteria' => 'The team can operate incidents from a runbook instead of tribal knowledge.',
            ],
            [
                'phase' => 'Phase 3: revisit architecture',
                'decision' => $input['team_maturity'] === 'platform'
                    ? 'Consider a broader platform primitive only after usage and ownership justify it.'
                    : 'Avoid a platform-style redesign until team capacity and business impact justify it.',
                'exit_criteria' => 'A review trigger fires and the rejected alternative is now cheaper than the current cost.',
            ],
        ];
    }

    /**
     * Return operational risks for the chosen style.
     *
     * @return array<int, array{risk: string, mitigation: string}>
     */
    private function riskRegisterFor(array $input, string $style): array
    {
        $risks = [
            [
                'risk' => 'Answer starts with technology before constraints.',
                'mitigation' => 'Ask latency, platforms, failure impact, consistency, team capacity, and deadline questions first.',
            ],
            [
                'risk' => 'The design is technically correct but impossible for the team to operate.',
                'mitigation' => 'Match the design to team maturity and write down the owner of each moving part.',
            ],
        ];

        if ($style === 'websocket_event_stream') {
            $risks[] = [
                'risk' => 'Connection fan-out or sticky-session assumptions fail under load.',
                'mitigation' => 'Use external pub/sub or broker fan-out, connection metrics, backpressure, and gateway health checks.',
            ];
        }

        if ($input['consistency_need'] === 'strong') {
            $risks[] = [
                'risk' => 'Duplicate requests create double charge, double order, or inconsistent state.',
                'mitigation' => 'Use idempotency keys, transactions, unique constraints, and reconciliation logs.',
            ];
        }

        return $risks;
    }

    /**
     * Describe who owns the design after the interview diagram becomes production.
     *
     * @return array{owner: string, runbook_focus: array<int, string>, metrics: array<int, string>, rollback: string}
     */
    private function operatingModelFor(array $input, string $style): array
    {
        return [
            'owner' => $input['team_maturity'] === 'platform'
                ? 'Platform team owns shared infrastructure; product team owns notification semantics.'
                : 'Feature team owns the narrow design and should avoid infrastructure it cannot operate.',
            'runbook_focus' => match ($style) {
                'websocket_event_stream' => ['connection count by gateway', 'fan-out lag', 'broker consumer lag', 'gateway restart behavior'],
                'event_driven_broker' => ['consumer lag', 'dead-letter queue', 'retry storm', 'schema compatibility'],
                'strong_consistency_first' => ['idempotency collision', 'transaction failure', 'reconciliation queue', 'duplicate prevention'],
                'vertical_scale_first' => ['CPU and memory ceiling', 'database wait time', 'manual rollback', 'review trigger for redesign'],
                default => ['polling rate', 'p95 response time', 'stale notification age', 'client retry behavior'],
            },
            'metrics' => match ($style) {
                'websocket_event_stream' => ['connected_clients', 'fanout_lag_ms', 'delivery_p99_ms', 'dropped_connections'],
                'event_driven_broker' => ['published_events', 'consumer_lag', 'retry_count', 'dead_letter_count'],
                'strong_consistency_first' => ['duplicate_rejected_count', 'transaction_retry_count', 'reconciliation_backlog'],
                'vertical_scale_first' => ['database_cpu', 'lock_wait_ms', 'p99_latency', 'capacity_headroom_percent'],
                default => ['poll_requests_per_minute', 'empty_poll_ratio', 'notification_age_p95'],
            },
            'rollback' => 'Roll back to the previous delivery path or disable the new channel behind a feature flag before data correctness is harmed.',
        ];
    }

    /**
     * Name the signals that should reopen the architecture decision.
     *
     * @return array<int, string>
     */
    private function decisionReviewTriggersFor(string $style): array
    {
        return match ($style) {
            'websocket_event_stream' => [
                'Connection gateways regularly exceed safe memory or file descriptor headroom.',
                'Fan-out lag breaches the product latency SLO.',
                'Most clients do not actually need live updates after measurement.',
            ],
            'event_driven_broker' => [
                'Consumer lag or retry storms become the primary incident source.',
                'Event schema changes slow delivery more than they protect decoupling.',
                'Throughput stays low enough that a simpler queue would be safer.',
            ],
            'strong_consistency_first' => [
                'Correctness incidents drop to zero and throughput becomes the clear bottleneck.',
                'The domain can formally accept eventual consistency for a subset of operations.',
            ],
            'vertical_scale_first' => [
                'Vertical headroom falls below the agreed threshold.',
                'The legacy bottleneck has enough tests and ownership to split safely.',
                'The interim deadline constraint no longer applies.',
            ],
            default => [
                'Polling traffic becomes more expensive than WebSocket gateway operations.',
                'User experience requires delivery latency below the agreed delay budget.',
                'The team gains enough operational maturity to carry a push architecture.',
            ],
        };
    }

    /**
     * Suggest interviewer follow-ups that pressure-test the answer.
     *
     * @return array<int, string>
     */
    private function interviewerFollowupsFor(string $style): array
    {
        $shared = [
            'What would make you reverse this decision in three months?',
            'What is the first metric you would put on a dashboard?',
            'Which part of this design is hardest for the team to operate?',
        ];

        return [
            ...$shared,
            match ($style) {
                'websocket_event_stream' => 'How do messages reach a user connected to a different gateway node?',
                'event_driven_broker' => 'How do you handle retries without sending duplicate notifications?',
                'strong_consistency_first' => 'Where do you put idempotency and reconciliation?',
                'vertical_scale_first' => 'How do you stop an interim vertical scale decision from becoming permanent technical debt?',
                default => 'When would polling become too expensive compared with push delivery?',
            },
        ];
    }

    /**
     * Build a short interviewer simulation with pushback and strong response moves.
     *
     * @param  array{style: string, label: string, reason: string}  $recommendation
     * @return array<int, array{interviewer: string, intent: string, strong_response: string}>
     */
    private function interviewerSimulationFor(array $input, array $recommendation): array
    {
        return [
            [
                'interviewer' => 'Why did you choose this approach instead of starting with the most scalable architecture?',
                'intent' => 'Tests whether the candidate understands cost and context rather than chasing scale vocabulary.',
                'strong_response' => sprintf(
                    'I chose %s because the current constraints make its cost acceptable. I would only move to a heavier architecture when the review trigger proves the current cost is worse.',
                    $recommendation['label'],
                ),
            ],
            [
                'interviewer' => 'What breaks first in production?',
                'intent' => 'Checks operational realism beyond the diagram.',
                'strong_response' => match ($recommendation['style']) {
                    'websocket_event_stream' => 'Connection fan-out, gateway memory, backpressure, or cross-node delivery breaks first, so I would watch fanout_lag_ms and dropped_connections.',
                    'event_driven_broker' => 'Consumer lag, retry storms, schema changes, or dead-letter queues break first, so I would watch consumer_lag and retry_count.',
                    'strong_consistency_first' => 'Duplicate attempts, transaction retries, and reconciliation backlog break first, so idempotency and audit trails come before throughput.',
                    'vertical_scale_first' => 'Capacity headroom and lock waits break first, so I would set a review trigger before vertical scaling becomes permanent debt.',
                    default => 'Polling cost, stale notification age, and client retry storms break first, so I would watch notification_age_p95 and empty_poll_ratio.',
                },
            ],
            [
                'interviewer' => 'What if the team says this is too hard to operate?',
                'intent' => 'Tests organization awareness and reversibility.',
                'strong_response' => $input['operational_capacity'] === 'low'
                    ? 'Then I would reduce moving parts and keep the design closer to HTTP or vertical scaling unless business impact forces the complexity.'
                    : 'Then I would name the owner, runbook, first metric, rollback path, and the condition that lets us simplify if the operational cost is too high.',
            ],
            [
                'interviewer' => 'What would make you change your mind?',
                'intent' => 'Checks whether the decision is reversible and measurable.',
                'strong_response' => $this->decisionReviewTriggersFor($recommendation['style'])[0],
            ],
        ];
    }

    /**
     * Give learners a before/after exercise for interview phrasing.
     *
     * @param  array{style: string, label: string, reason: string}  $recommendation
     * @return array{weak_answer: string, stronger_answer: string, rule: string}
     */
    private function rewritePracticeFor(array $recommendation): array
    {
        return [
            'weak_answer' => sprintf('I choose %s because it scales well.', $recommendation['label']),
            'stronger_answer' => sprintf('I choose %s because it fits the stated constraints; the cost is explicit, and I would revisit it when the review triggers appear.', $recommendation['label']),
            'rule' => 'Replace tool-first claims with context, accepted cost, rejected alternative, owner, and review trigger.',
        ];
    }

    /**
     * Return a structured answer contract that can be reused in interview notes.
     *
     * @param  array{style: string, label: string, reason: string}  $recommendation
     * @return array{format: array<int, string>, filled_example: array<string, string>}
     */
    private function answerContractFor(array $input, array $recommendation): array
    {
        $owner = $input['team_maturity'] === 'platform'
            ? 'platform team owns infrastructure; product team owns notification semantics'
            : 'feature team owns the narrow path and avoids platform complexity';

        return [
            'format' => [
                'Context and constraints',
                'Decision',
                'Accepted cost',
                'Rejected alternative',
                'Operational owner',
                'First metric',
                'Review trigger',
            ],
            'filled_example' => [
                'context' => sprintf('%s scenario with %s latency, %s consistency, %s failure impact, and %s operational capacity.', $input['scenario'], $input['latency_requirement'], $input['consistency_need'], $input['failure_impact'], $input['operational_capacity']),
                'decision' => $recommendation['label'],
                'accepted_cost' => $this->costFor($recommendation['style']),
                'rejected_alternative' => $input['operational_capacity'] === 'low'
                    ? 'a distributed design the team cannot safely operate'
                    : 'paying complexity before the business constraint proves it is needed',
                'owner' => $owner,
                'first_metric' => $this->firstMetricFor($recommendation['style']),
                'review_trigger' => $this->decisionReviewTriggersFor($recommendation['style'])[0],
            ],
        ];
    }

    /**
     * Return a pre-submit checklist for judging one answer.
     *
     * @return array<int, array{item: string, pass_condition: string}>
     */
    private function reviewChecklistFor(string $style): array
    {
        return [
            [
                'item' => 'Clarifying questions',
                'pass_condition' => 'At least three answers would change the architecture if they changed.',
            ],
            [
                'item' => 'Tradeoff sentence',
                'pass_condition' => 'The answer explicitly says what cost is accepted and why the rejected cost is worse.',
            ],
            [
                'item' => 'Operational reality',
                'pass_condition' => 'The answer names owner, dashboard metric, runbook focus, and rollback path.',
            ],
            [
                'item' => 'Failure mode',
                'pass_condition' => match ($style) {
                    'websocket_event_stream' => 'Fan-out, connection loss, backpressure, and gateway failover are mentioned.',
                    'event_driven_broker' => 'Consumer lag, retry storm, duplicate delivery, and dead-letter handling are mentioned.',
                    'strong_consistency_first' => 'Idempotency, transaction failure, duplicate prevention, and reconciliation are mentioned.',
                    'vertical_scale_first' => 'Capacity ceiling, interim nature, and split trigger are mentioned.',
                    default => 'Delay budget, polling cost, client retry behavior, and push trigger are mentioned.',
                },
            ],
        ];
    }

    /**
     * Build an AI/interviewer practice prompt for the same scenario.
     *
     * @return array{prompt: string, expected_output: array<int, string>}
     */
    private function practicePromptFor(array $input): array
    {
        return [
            'prompt' => sprintf(
                'Interview me on System Design for %s. Push me to ask clarifying questions first, then make me defend latency, consistency, operations, team maturity, metrics, rollback, and a review trigger.',
                $input['scenario'],
            ),
            'expected_output' => [
                'Clarifying questions before architecture',
                'One explicit decision',
                'Accepted and rejected costs',
                'Operational owner and metric',
                'Follow-up questions that expose weak assumptions',
            ],
        ];
    }

    /**
     * Return weak, acceptable, and strong answer examples.
     *
     * @return array<int, array{level: string, answer: string, signal: string}>
     */
    private function calibrationExamplesFor(string $style): array
    {
        $label = match ($style) {
            'websocket_event_stream' => 'WebSocket plus event stream',
            'event_driven_broker' => 'event-driven broker',
            'strong_consistency_first' => 'strong consistency and idempotency first',
            'vertical_scale_first' => 'vertical scaling first',
            default => 'Long Polling first',
        };

        return [
            [
                'level' => 'weak',
                'answer' => sprintf('I would use %s because it scales.', $label),
                'signal' => 'Names a technology or pattern without context.',
            ],
            [
                'level' => 'acceptable',
                'answer' => sprintf('I would use %s if the constraints require it, but I would compare it against a simpler option.', $label),
                'signal' => 'Shows comparison but still needs ownership and metrics.',
            ],
            [
                'level' => 'strong',
                'answer' => sprintf('I would choose %s only after confirming constraints; I would state the accepted operational cost, owner, first metric, rollback, and review trigger.', $label),
                'signal' => 'Connects architecture to business impact, operations, and reversibility.',
            ],
        ];
    }

    /**
     * Build a timed spoken answer for interview rehearsal.
     *
     * @param  array{style: string, label: string, reason: string}  $recommendation
     */
    private function timedAnswerFor(array $input, array $recommendation, string $mode): string
    {
        $contract = $this->answerContractFor($input, $recommendation)['filled_example'];

        if ($mode === 'one-minute') {
            return sprintf(
                'I would not draw first. I would clarify latency, platforms, outage impact, duplicate tolerance, and team capacity. Given this context, I choose %s. The accepted cost is %s; I reject %s because it costs more here. I would watch %s and revisit the decision when %s',
                $contract['decision'],
                $contract['accepted_cost'],
                $contract['rejected_alternative'],
                $contract['first_metric'],
                $contract['review_trigger'],
            );
        }

        return sprintf(
            'First I would clarify whether the system needs real-time delivery, which clients receive notifications, what a five-minute outage costs, and whether duplicates are acceptable. For this %s scenario, I would choose %s. The tradeoff is %s. I accept that because the rejected alternative is %s. Ownership matters: %s. I would start with the smallest safe design, measure %s, keep a rollback path, and reopen the decision when %s',
            $input['scenario'],
            $contract['decision'],
            $contract['accepted_cost'],
            $contract['rejected_alternative'],
            $contract['owner'],
            $contract['first_metric'],
            $contract['review_trigger'],
        );
    }

    /**
     * Return short repetition cards for daily interview practice.
     *
     * @return array<int, array{front: string, back: string}>
     */
    private function drillCardsFor(string $style): array
    {
        return [
            [
                'front' => 'What is the first move in a System Design interview?',
                'back' => 'Clarify constraints before drawing boxes or naming technology.',
            ],
            [
                'front' => 'What sentence proves tradeoff thinking?',
                'back' => 'I choose A; the cost is X; I accept it because Y costs more in this context.',
            ],
            [
                'front' => 'What production detail should follow the diagram?',
                'back' => sprintf('Owner, first metric, rollback path, and the %s-specific failure mode.', $style),
            ],
            [
                'front' => 'How do you avoid sounding like you memorized Kafka/WebSocket/Redis?',
                'back' => 'Compare one rejected alternative and say why the team can operate the chosen design.',
            ],
        ];
    }

    /**
     * Return scenario changes that learners can rehearse after the first answer.
     *
     * @return array<int, array{change: string, expected_shift: string}>
     */
    private function scenarioVariationsFor(array $input): array
    {
        return [
            [
                'change' => 'Change latency from real-time to delayed by 30 seconds.',
                'expected_shift' => $input['latency_requirement'] === 'real-time'
                    ? 'Reconsider WebSocket and test whether Long Polling or async delivery is cheaper.'
                    : 'Confirm the simpler delivery path still meets the product delay budget.',
            ],
            [
                'change' => 'Change consistency from eventual to strong.',
                'expected_shift' => $input['consistency_need'] === 'strong'
                    ? 'Keep idempotency and reconciliation central; optimize throughput later.'
                    : 'Add idempotency keys, transactions, unique constraints, and reconciliation before scaling fan-out.',
            ],
            [
                'change' => 'Shrink the team to three engineers without platform support.',
                'expected_shift' => 'Reduce moving parts, avoid broker/WebSocket operations unless business impact proves the cost.',
            ],
            [
                'change' => 'Introduce a legacy database that cannot be split in this quarter.',
                'expected_shift' => 'Consider vertical scaling, measurement, and a future split trigger instead of immediate distributed redesign.',
            ],
        ];
    }

    /**
     * Interpret the self-review scorecard as interview readiness bands.
     *
     * @param  array{max_score: int, passing_score: int, dimensions: array<int, array{name: string, points: int, evidence: string}>}  $scorecard
     * @return array<int, array{range: string, signal: string}>
     */
    private function scoreInterpretationFor(array $scorecard): array
    {
        return [
            [
                'range' => '0-9',
                'signal' => 'Tool-first or memorized answer. Rebuild from clarifying questions.',
            ],
            [
                'range' => '10-14',
                'signal' => 'Acceptable mid-level answer. Add rejected alternatives, owner, and review trigger.',
            ],
            [
                'range' => sprintf('%d-%d', $scorecard['passing_score'], $scorecard['max_score']),
                'signal' => 'Senior-ready answer when backed by concrete failure modes and metrics.',
            ],
        ];
    }

    /**
     * Build a portable markdown packet for interview rehearsal notes.
     *
     * @param  array{style: string, label: string, reason: string}  $recommendation
     */
    private function interviewPacketMarkdownFor(array $input, array $recommendation): string
    {
        $contract = $this->answerContractFor($input, $recommendation)['filled_example'];
        $checklist = collect($this->reviewChecklistFor($recommendation['style']))
            ->map(fn (array $item): string => sprintf('- %s: %s', $item['item'], $item['pass_condition']))
            ->implode("\n");
        $followups = collect($this->interviewerFollowupsFor($recommendation['style']))
            ->map(fn (string $question): string => '- '.$question)
            ->implode("\n");

        return <<<MARKDOWN
# System Design Interview Packet: {$input['scenario']}

## One-minute answer
{$this->timedAnswerFor($input, $recommendation, 'one-minute')}

## Two-minute answer
{$this->timedAnswerFor($input, $recommendation, 'two-minute')}

## Answer contract
- Context: {$contract['context']}
- Decision: {$contract['decision']}
- Accepted cost: {$contract['accepted_cost']}
- Rejected alternative: {$contract['rejected_alternative']}
- Owner: {$contract['owner']}
- First metric: {$contract['first_metric']}
- Review trigger: {$contract['review_trigger']}

## Review checklist
{$checklist}

## Interviewer follow-ups
{$followups}
MARKDOWN;
    }

    /**
     * Return the main cost for one recommendation style.
     */
    private function costFor(string $style): string
    {
        return match ($style) {
            'websocket_event_stream' => 'connection state, fan-out, backpressure, and gateway operations',
            'event_driven_broker' => 'broker operations, schema governance, retries, and consumer lag',
            'vertical_scale_first' => 'limited long-term scalability and a planned revisit point',
            'strong_consistency_first' => 'lower throughput and more stateful correctness checks',
            default => 'less immediate delivery and more polling traffic',
        };
    }

    /**
     * Return the first dashboard metric to watch.
     */
    private function firstMetricFor(string $style): string
    {
        return match ($style) {
            'websocket_event_stream' => 'fanout_lag_ms',
            'event_driven_broker' => 'consumer_lag',
            'vertical_scale_first' => 'capacity_headroom_percent',
            'strong_consistency_first' => 'duplicate_rejected_count',
            default => 'notification_age_p95',
        };
    }

    /**
     * Show how the same answer evolves by interview level.
     *
     * @return array<int, array{level: string, signal: string}>
     */
    private function levelFramingFor(string $label): array
    {
        return [
            ['level' => 'L4', 'signal' => sprintf('Names the implementation: %s.', $label)],
            ['level' => 'L5', 'signal' => 'Compares two viable options and explains pros and cons.'],
            ['level' => 'L6', 'signal' => 'Proactively raises delivery guarantees, operational ownership, and failure modes.'],
            ['level' => 'L7', 'signal' => 'Frames the design around business impact, organization maturity, SLA, and cost of being wrong.'],
        ];
    }

    /**
     * Return the upgrade plan for the learner's target interview level.
     *
     * @return array{target_level: string, expected_signal: string, must_add: array<int, string>, answer_opening: string}
     */
    private function targetLevelPlanFor(string $targetLevel, string $label): array
    {
        return match ($targetLevel) {
            'l4' => [
                'target_level' => 'L4',
                'expected_signal' => 'Can propose a workable architecture and explain the main components.',
                'must_add' => [
                    sprintf('Name the implementation clearly: %s.', $label),
                    'Explain the basic request or event flow.',
                    'Avoid adding components you cannot explain.',
                ],
                'answer_opening' => 'I would start with the simplest workable design and explain the main flow.',
            ],
            'l5' => [
                'target_level' => 'L5',
                'expected_signal' => 'Compares viable options and states practical pros and cons.',
                'must_add' => [
                    'Compare the chosen design against one simpler alternative.',
                    'State latency, consistency, and operational tradeoffs.',
                    'Name the main failure mode and mitigation.',
                ],
                'answer_opening' => 'I see two reasonable options here; I would choose one based on the constraints.',
            ],
            'l7' => [
                'target_level' => 'L7',
                'expected_signal' => 'Frames architecture through business impact, organization maturity, SLA, and reversibility.',
                'must_add' => [
                    'Start from business harm and SLA, not technology.',
                    'Name who owns the cost after launch.',
                    'Define the decision review trigger and organizational constraint.',
                    'Explain why the rejected option is more expensive for this company right now.',
                ],
                'answer_opening' => 'Before architecture, I would clarify the business impact, SLA, organization maturity, and cost of being wrong.',
            ],
            default => [
                'target_level' => 'L6',
                'expected_signal' => 'Proactively exposes hidden tradeoffs before the interviewer asks.',
                'must_add' => [
                    'Raise delivery guarantees, idempotency, and replay semantics.',
                    'Name operational ownership, metrics, rollback, and runbook focus.',
                    'Describe the failure mode that would page the team.',
                    'State the review trigger that would change the design.',
                ],
                'answer_opening' => 'Before choosing the component, I want to pin down the guarantees and operational owner.',
            ],
        };
    }

    /**
     * Score a learner-written answer against the tradeoff rubric.
     *
     * @return array{status: string, score: int, max_score: int, band: string, matched: array<int, string>, missing: array<int, string>, evidence_spans: array<int, array{dimension: string, evidence: string}>, rewrite_outline: array<int, string>, gap_drills: array<int, array{gap: string, drill: string}>, review_markdown: string, next_rewrite: string}
     */
    private function candidateAnswerReviewFor(?string $answer): array
    {
        $answer = trim((string) $answer);

        if ($answer === '') {
            return [
                'status' => 'not-submitted',
                'score' => 0,
                'max_score' => 20,
                'band' => 'no-answer',
                'matched' => [],
                'missing' => ['Paste your own answer to get a rubric review.'],
                'evidence_spans' => [],
                'rewrite_outline' => $this->candidateRewriteOutlineFor(),
                'gap_drills' => [
                    [
                        'gap' => 'no answer',
                        'drill' => 'Record a 60-second answer using the rewrite outline, then paste it back for scoring.',
                    ],
                ],
                'review_markdown' => $this->candidateReviewMarkdownFor('not-submitted', 0, [], ['Paste your own answer to get a rubric review.'], []),
                'next_rewrite' => 'Write a 60-second answer using: context -> decision -> accepted cost -> rejected alternative -> owner -> metric -> review trigger.',
            ];
        }

        $lower = strtolower($answer);
        $checks = [
            'clarifying constraints' => [
                'points' => 4,
                'keywords' => ['latency', 'platform', 'outage', 'requirement'],
                'missing' => 'Ask or state clarifying constraints before drawing the architecture.',
            ],
            'explicit tradeoff' => [
                'points' => 4,
                'keywords' => ['tradeoff', 'cost', 'price'],
                'missing' => 'Name the accepted cost of the chosen design.',
            ],
            'rejected alternative' => [
                'points' => 3,
                'keywords' => ['instead', 'rather', 'alternative', 'reject'],
                'missing' => 'Compare against one rejected alternative.',
            ],
            'operational owner' => [
                'points' => 3,
                'keywords' => ['team', 'owner', 'operate', 'on-call'],
                'missing' => 'Name who can operate the design after launch.',
            ],
            'metric or review trigger' => [
                'points' => 3,
                'keywords' => ['metric', 'monitor', 'dashboard', 'revisit', 'trigger'],
                'missing' => 'Add one metric or trigger that would change the decision.',
            ],
            'failure mode' => [
                'points' => 3,
                'keywords' => ['failure', 'duplicate', 'lag', 'rollback', 'backpressure'],
                'missing' => 'Expose the hardest failure mode and mitigation.',
            ],
        ];

        $score = 0;
        $matched = [];
        $missing = [];
        $evidenceSpans = [];

        foreach ($checks as $name => $check) {
            $evidence = $this->candidateEvidenceFor($answer, $check['keywords']);

            if ($evidence !== null) {
                $score += $check['points'];
                $matched[] = $name;
                $evidenceSpans[] = [
                    'dimension' => $name,
                    'evidence' => $evidence,
                ];
            } else {
                $missing[] = $check['missing'];
            }
        }

        $band = match (true) {
            $score >= 15 => 'senior-ready',
            $score >= 10 => 'needs-senior-detail',
            default => 'tool-first-or-incomplete',
        };

        $gapDrills = $this->candidateGapDrillsFor($missing);

        return [
            'status' => 'reviewed',
            'score' => $score,
            'max_score' => 20,
            'band' => $band,
            'matched' => $matched,
            'missing' => $missing,
            'evidence_spans' => $evidenceSpans,
            'rewrite_outline' => $this->candidateRewriteOutlineFor($missing),
            'gap_drills' => $gapDrills,
            'review_markdown' => $this->candidateReviewMarkdownFor($band, $score, $matched, $missing, $evidenceSpans),
            'next_rewrite' => $missing === []
                ? 'Compress the same answer into a clear 60-second version without losing the tradeoff.'
                : 'Rewrite by adding: '.implode(' ', $missing),
        ];
    }

    /**
     * Return the answer fragment that matched one rubric dimension.
     *
     * @param  array<int, string>  $keywords
     */
    private function candidateEvidenceFor(string $answer, array $keywords): ?string
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', trim($answer)) ?: [];

        foreach ($sentences as $sentence) {
            $lowerSentence = strtolower($sentence);

            foreach ($keywords as $keyword) {
                if (str_contains($lowerSentence, $keyword)) {
                    return trim($sentence);
                }
            }
        }

        return null;
    }

    /**
     * Build a structured rewrite outline from missing dimensions.
     *
     * @param  array<int, string>  $missing
     * @return array<int, string>
     */
    private function candidateRewriteOutlineFor(array $missing = []): array
    {
        $base = [
            'Context: state latency, platform, outage impact, duplicate tolerance, and team capacity.',
            'Decision: name the chosen architecture in one sentence.',
            'Tradeoff: state the accepted cost and why it is acceptable.',
            'Alternative: name the rejected option and why it costs more here.',
            'Operations: name owner, metric, rollback, and review trigger.',
            'Failure mode: name the incident you expect and the mitigation.',
        ];

        if ($missing === []) {
            return $base;
        }

        return [
            'Start by fixing the missing rubric items.',
            ...$missing,
            'Then compress the full answer into the standard context -> decision -> tradeoff -> owner -> metric structure.',
        ];
    }

    /**
     * Return focused drills for the missing answer dimensions.
     *
     * @param  array<int, string>  $missing
     * @return array<int, array{gap: string, drill: string}>
     */
    private function candidateGapDrillsFor(array $missing): array
    {
        if ($missing === []) {
            return [
                [
                    'gap' => 'compression',
                    'drill' => 'Cut the answer to 60 seconds while keeping one accepted cost, one rejected alternative, one owner, and one metric.',
                ],
            ];
        }

        return collect($missing)
            ->map(fn (string $gap): array => [
                'gap' => $gap,
                'drill' => match (true) {
                    str_contains($gap, 'constraints') => 'Add two clarifying questions that could change the architecture.',
                    str_contains($gap, 'accepted cost') => 'Add one sentence starting with: The price I accept is...',
                    str_contains($gap, 'rejected alternative') => 'Add one sentence comparing the chosen design to a simpler or stronger alternative.',
                    str_contains($gap, 'operate') => 'Add one owner and one runbook responsibility.',
                    str_contains($gap, 'metric') => 'Add one metric and one trigger for revisiting the decision.',
                    str_contains($gap, 'failure mode') => 'Add one failure mode and one mitigation.',
                    default => 'Rewrite this missing part as one concrete interview sentence.',
                },
            ])
            ->values()
            ->all();
    }

    /**
     * Build markdown that can be copied into interview notes.
     *
     * @param  array<int, string>  $matched
     * @param  array<int, string>  $missing
     * @param  array<int, array{dimension: string, evidence: string}>  $evidenceSpans
     */
    private function candidateReviewMarkdownFor(string $band, int $score, array $matched, array $missing, array $evidenceSpans): string
    {
        $matchedText = $matched === []
            ? '- none'
            : collect($matched)->map(fn (string $item): string => '- '.$item)->implode("\n");
        $missingText = $missing === []
            ? '- none'
            : collect($missing)->map(fn (string $item): string => '- '.$item)->implode("\n");
        $evidenceText = $evidenceSpans === []
            ? '- no matched evidence yet'
            : collect($evidenceSpans)
                ->map(fn (array $item): string => sprintf('- %s: %s', $item['dimension'], $item['evidence']))
                ->implode("\n");

        return <<<MARKDOWN
# Candidate System Design Answer Review

Score: {$score}/20
Band: {$band}

## Matched
{$matchedText}

## Missing
{$missingText}

## Evidence
{$evidenceText}
MARKDOWN;
    }

    /**
     * Build a concise interview answer.
     *
     * @param  array{style: string, label: string, reason: string}  $recommendation
     */
    private function interviewAnswerFor(array $input, array $recommendation): string
    {
        return sprintf(
            'Before drawing the notification system, I would ask about latency, platforms, five-minute outage impact, duplicate tolerance, and team maturity. Given %s latency, %s consistency, %s failure impact, and %s operational capacity, I would start with: %s. %s',
            $input['latency_requirement'],
            $input['consistency_need'],
            $input['failure_impact'],
            $input['operational_capacity'],
            $recommendation['label'],
            $this->tradeoffStatementFor($input, $recommendation)
        );
    }

    /**
     * Create a markdown decision memo.
     *
     * @param  array{style: string, label: string, reason: string}  $recommendation
     * @param  array{long_polling: int, websocket_event_stream: int, event_driven_broker: int, vertical_scale_first: int, strong_consistency_first: int, signals: array<int, array{signal: string, note: string}>}  $scores
     */
    private function decisionMemoFor(array $input, array $recommendation, array $scores): string
    {
        $signals = collect($scores['signals'])
            ->map(fn (array $signal): string => sprintf('- %s: %s', $signal['signal'], $signal['note']))
            ->implode("\n");
        $tradeoff = $this->tradeoffStatementFor($input, $recommendation);

        return <<<MARKDOWN
# ADR: System Design tradeoff for {$input['scenario']}

## Decision
{$recommendation['label']}

## Why
{$recommendation['reason']}

## Tradeoff
{$tradeoff}

## Signals
{$signals}

## Review Questions
- If this is wrong, who pays?
- Where does the complexity live?
- Can this team carry it after launch?
MARKDOWN;
    }
}
