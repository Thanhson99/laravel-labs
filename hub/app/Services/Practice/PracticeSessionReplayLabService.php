<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeSessionReplayLabService
{
    /**
     * Build replay rounds from next-session handoff cards.
     */
    public function __construct(
        private readonly PracticeNextSessionHandoffLabService $handoffs,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create executable replay rounds for the next Laravel session.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $handoffLab = $this->handoffs->build($filters);

        $rounds = collect($handoffLab['handoff_cards'])
            ->values()
            ->map(fn (array $card): array => [
                'round' => $card['step'],
                'technology_segment' => $card['technology_segment'],
                'route_name' => $card['open_route_name'],
                'handoff_type' => $card['handoff_type'],
                'before_check' => $this->beforeCheckFor($card),
                'coding_run' => $this->codingRunFor($card),
                'after_check' => $this->afterCheckFor($card),
                'evidence_capture' => $this->evidenceCaptureFor($card),
                'retry_policy' => $this->retryPolicyFor($card),
            ])
            ->all();

        return [
            'title' => 'Laravel session replay practice lab',
            'source_handoff_lab' => [
                'title' => $handoffLab['title'],
                'handoff_count' => $handoffLab['handoff_summary']['handoff_count'],
                'promotion_handoffs' => $handoffLab['handoff_summary']['promotion_handoffs'],
                'repeat_handoffs' => $handoffLab['handoff_summary']['repeat_handoffs'],
            ],
            'replay_rules' => [
                'Run the before-check before editing so failures are visible.',
                'Replay one handoff card at a time.',
                'Capture route output and command output after each run.',
                'Use the retry policy immediately when the after-check fails.',
            ],
            'replay_rounds' => $rounds,
            'replay_summary' => [
                'round_count' => count($rounds),
                'promotion_rounds' => collect($rounds)->where('handoff_type', 'promotion-handoff')->count(),
                'repeat_rounds' => collect($rounds)->where('handoff_type', 'repeat-handoff')->count(),
                'commands_to_run' => collect($rounds)->pluck('after_check.command')->unique()->values()->all(),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $rounds,
                fn (array $round): string => sprintf('Replayed session round %d: %s', $round['round'], $round['technology_segment'])
            ),
        ];
    }

    /**
     * Build the before-check for one replay round.
     *
     * @param  array{carry_over_route_name: string, verification_command: string}  $card
     * @return array<string, string>
     */
    private function beforeCheckFor(array $card): array
    {
        return [
            'route_name' => $card['carry_over_route_name'],
            'command' => $card['verification_command'],
            'expected_result' => 'Known baseline is visible before editing.',
        ];
    }

    /**
     * Build the coding run for one replay round.
     *
     * @param  array{session_goal: string, coding_focus: array<int, string>}  $card
     * @return array<string, mixed>
     */
    private function codingRunFor(array $card): array
    {
        return [
            'goal' => $card['session_goal'],
            'focus_items' => $card['coding_focus'],
            'timebox_minutes' => 30,
        ];
    }

    /**
     * Build the after-check for one replay round.
     *
     * @param  array{open_route_name: string, verification_command: string}  $card
     * @return array<string, string>
     */
    private function afterCheckFor(array $card): array
    {
        return [
            'route_name' => $card['open_route_name'],
            'command' => $card['verification_command'],
            'expected_result' => 'Changed behavior and verification output are both captured.',
        ];
    }

    /**
     * Build evidence capture tasks for one replay round.
     *
     * @param  array{done_evidence: array<int, string>, handoff_note_prompt: string}  $card
     * @return array<int, string>
     */
    private function evidenceCaptureFor(array $card): array
    {
        return [
            $card['done_evidence'][0],
            $card['done_evidence'][1],
            $card['handoff_note_prompt'],
        ];
    }

    /**
     * Build retry policy for one replay round.
     *
     * @param  array{handoff_type: string, open_route_name: string}  $card
     */
    private function retryPolicyFor(array $card): string
    {
        if ($card['handoff_type'] === 'promotion-handoff') {
            return sprintf('If promotion proof is weak, return to `%s` and rewrite the evidence before adding new scope.', $card['open_route_name']);
        }

        return sprintf('If repeat evidence still fails, stay on `%s` and reduce the scope to one assertion.', $card['open_route_name']);
    }
}
