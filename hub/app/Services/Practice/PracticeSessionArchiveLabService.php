<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeSessionArchiveLabService
{
    /**
     * Build archive entries from session debrief cards.
     */
    public function __construct(
        private readonly PracticeSessionDebriefLabService $debriefs,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create archive entries for completed Laravel practice sessions.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $debriefLab = $this->debriefs->build($filters);

        $entries = collect($debriefLab['debrief_cards'])
            ->values()
            ->map(fn (array $card): array => [
                'entry' => $card['round'],
                'technology_segment' => $card['technology_segment'],
                'route_name' => $card['route_name'],
                'command' => $card['command'],
                'archive_status' => $this->archiveStatusFor($card),
                'proof_bundle' => $this->proofBundleFor($card),
                'learning_summary' => $this->learningSummaryFor($card),
                'blocker_status' => $this->blockerStatusFor($card),
                'retrieval_tags' => $this->retrievalTagsFor($card),
                'next_reference' => $card['next_action'],
            ])
            ->all();

        return [
            'title' => 'Laravel session archive practice lab',
            'source_debrief_lab' => [
                'title' => $debriefLab['title'],
                'card_count' => $debriefLab['debrief_summary']['card_count'],
                'passed_review_count' => $debriefLab['debrief_summary']['passed_review_count'],
                'needs_retry_count' => $debriefLab['debrief_summary']['needs_retry_count'],
            ],
            'archive_rules' => [
                'Archive only evidence that names a route, command, and learned behavior.',
                'Keep blocker status visible even when the session passed.',
                'Add retrieval tags that match technology, route, and interview use.',
                'Every archive entry must point to a next reference.',
            ],
            'archive_entries' => $entries,
            'archive_summary' => [
                'entry_count' => count($entries),
                'archived_count' => collect($entries)->where('archive_status', 'archived')->count(),
                'retry_archive_count' => collect($entries)->where('archive_status', 'retry-before-archive')->count(),
                'routes_archived' => collect($entries)->pluck('route_name')->unique()->values()->all(),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $entries,
                fn (array $entry): string => sprintf('Archived session entry %d: %s', $entry['entry'], $entry['technology_segment'])
            ),
        ];
    }

    /**
     * Decide archive status from debrief status.
     *
     * @param  array{debrief_status: string}  $card
     */
    private function archiveStatusFor(array $card): string
    {
        return $card['debrief_status'] === 'ready-for-archive'
            ? 'archived'
            : 'retry-before-archive';
    }

    /**
     * Build the proof bundle for an archive entry.
     *
     * @param  array{route_name: string, command: string, result_notes: array<int, string>}  $card
     * @return array<int, string>
     */
    private function proofBundleFor(array $card): array
    {
        return [
            sprintf('Route proof: `%s`.', $card['route_name']),
            sprintf('Command proof: `%s`.', $card['command']),
            $card['result_notes'][0],
        ];
    }

    /**
     * Build learning summary prompts for an archive entry.
     *
     * @param  array{lesson_prompts: array<int, string>}  $card
     * @return array<int, string>
     */
    private function learningSummaryFor(array $card): array
    {
        return [
            $card['lesson_prompts'][0],
            $card['lesson_prompts'][1],
            'Write one reusable rule for the next implementation.',
        ];
    }

    /**
     * Build blocker status for an archive entry.
     *
     * @param  array{debrief_status: string, blocker_checks: array<int, string>}  $card
     * @return array<string, mixed>
     */
    private function blockerStatusFor(array $card): array
    {
        return [
            'status' => $card['debrief_status'] === 'ready-for-archive' ? 'cleared' : 'open',
            'checks' => $card['blocker_checks'],
        ];
    }

    /**
     * Build retrieval tags for future portfolio or interview use.
     *
     * @param  array{technology_segment: string, route_name: string, debrief_status: string}  $card
     * @return array<int, string>
     */
    private function retrievalTagsFor(array $card): array
    {
        return [
            $card['technology_segment'],
            $card['route_name'],
            $card['debrief_status'],
            'portfolio-evidence',
            'interview-defense',
        ];
    }
}
