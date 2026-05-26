<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeSessionDebriefLabService
{
    /**
     * Build debrief cards from session replay rounds.
     */
    public function __construct(
        private readonly PracticeSessionReplayLabService $replays,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create debrief cards after replaying a Laravel practice session.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $replayLab = $this->replays->build($filters);

        $cards = collect($replayLab['replay_rounds'])
            ->values()
            ->map(fn (array $round): array => [
                'round' => $round['round'],
                'technology_segment' => $round['technology_segment'],
                'route_name' => $round['route_name'],
                'command' => $round['after_check']['command'],
                'debrief_status' => $this->statusFor($round),
                'result_notes' => $this->resultNotesFor($round),
                'lesson_prompts' => $this->lessonPromptsFor($round),
                'blocker_checks' => $this->blockerChecksFor($round),
                'next_action' => $this->nextActionFor($round),
            ])
            ->all();

        return [
            'title' => 'Laravel session debrief practice lab',
            'source_replay_lab' => [
                'title' => $replayLab['title'],
                'round_count' => $replayLab['replay_summary']['round_count'],
                'promotion_rounds' => $replayLab['replay_summary']['promotion_rounds'],
                'repeat_rounds' => $replayLab['replay_summary']['repeat_rounds'],
            ],
            'debrief_rules' => [
                'Debrief immediately after the replay round while evidence is fresh.',
                'Keep notes tied to route output, command output, and changed behavior.',
                'Name blockers as code, test, content, or explanation blockers.',
                'End every debrief with one next action.',
            ],
            'debrief_cards' => $cards,
            'debrief_summary' => [
                'card_count' => count($cards),
                'passed_review_count' => collect($cards)->where('debrief_status', 'ready-for-archive')->count(),
                'needs_retry_count' => collect($cards)->where('debrief_status', 'needs-retry')->count(),
                'commands_reviewed' => collect($cards)->pluck('command')->unique()->values()->all(),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $cards,
                fn (array $card): string => sprintf('Debriefed replay round %d: %s', $card['round'], $card['technology_segment'])
            ),
        ];
    }

    /**
     * Decide the debrief status for one replay round.
     *
     * @param  array{handoff_type: string}  $round
     */
    private function statusFor(array $round): string
    {
        return $round['handoff_type'] === 'promotion-handoff'
            ? 'ready-for-archive'
            : 'needs-retry';
    }

    /**
     * Build concrete result notes to fill after a replay.
     *
     * @param  array{after_check: array<string, string>, evidence_capture: array<int, string>}  $round
     * @return array<int, string>
     */
    private function resultNotesFor(array $round): array
    {
        return [
            sprintf('Record whether `%s` passed and paste the important result line.', $round['after_check']['command']),
            $round['after_check']['expected_result'],
            $round['evidence_capture'][0],
        ];
    }

    /**
     * Build learning prompts for one replay.
     *
     * @param  array{technology_segment: string, coding_run: array<string, mixed>}  $round
     * @return array<int, string>
     */
    private function lessonPromptsFor(array $round): array
    {
        return [
            sprintf('What did this replay prove about %s?', $round['technology_segment']),
            sprintf('Which coding focus item mattered most: %s', $round['coding_run']['focus_items'][0]),
            'What would you explain differently in an interview after this replay?',
        ];
    }

    /**
     * Build blocker checks for one replay.
     *
     * @param  array{before_check: array<string, string>, after_check: array<string, string>}  $round
     * @return array<int, string>
     */
    private function blockerChecksFor(array $round): array
    {
        return [
            sprintf('Before-check route `%s` was unclear or stale.', $round['before_check']['route_name']),
            sprintf('After-check route `%s` did not show the expected behavior.', $round['after_check']['route_name']),
            'The command output did not match the written evidence.',
        ];
    }

    /**
     * Pick the next action after debrief.
     *
     * @param  array{handoff_type: string, route_name: string, retry_policy: string}  $round
     */
    private function nextActionFor(array $round): string
    {
        if ($round['handoff_type'] === 'promotion-handoff') {
            return sprintf('Archive the replay evidence and continue from `%s` in the next portfolio or demo lab.', $round['route_name']);
        }

        return $round['retry_policy'];
    }
}
