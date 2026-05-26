<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeArchiveRetrievalLabService
{
    /**
     * Build retrieval cards from archived practice session entries.
     */
    public function __construct(
        private readonly PracticeSessionArchiveLabService $archives,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create retrieval cards for portfolio, interview, and review use.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $archiveLab = $this->archives->build($filters);

        $cards = collect($archiveLab['archive_entries'])
            ->values()
            ->map(fn (array $entry): array => [
                'card' => $entry['entry'],
                'technology_segment' => $entry['technology_segment'],
                'route_name' => $entry['route_name'],
                'archive_status' => $entry['archive_status'],
                'retrieval_mode' => $this->retrievalModeFor($entry),
                'search_keys' => $this->searchKeysFor($entry),
                'retrieval_prompt' => $this->retrievalPromptFor($entry),
                'reuse_targets' => $this->reuseTargetsFor($entry),
                'proof_to_quote' => $entry['proof_bundle'],
                'refresh_check' => $this->refreshCheckFor($entry),
            ])
            ->all();

        return [
            'title' => 'Laravel archive retrieval practice lab',
            'source_archive_lab' => [
                'title' => $archiveLab['title'],
                'entry_count' => $archiveLab['archive_summary']['entry_count'],
                'archived_count' => $archiveLab['archive_summary']['archived_count'],
                'retry_archive_count' => $archiveLab['archive_summary']['retry_archive_count'],
            ],
            'retrieval_rules' => [
                'Retrieve archived evidence by technology, route, and use case.',
                'Quote only proof that includes route and command context.',
                'Refresh stale evidence before using it in portfolio or interview answers.',
                'Keep blocker status visible when reusing an archive entry.',
            ],
            'retrieval_cards' => $cards,
            'retrieval_summary' => [
                'card_count' => count($cards),
                'portfolio_ready_count' => collect($cards)->where('retrieval_mode', 'portfolio-ready')->count(),
                'refresh_required_count' => collect($cards)->where('retrieval_mode', 'refresh-before-use')->count(),
                'unique_search_keys' => collect($cards)->flatMap(fn (array $card): array => $card['search_keys'])->unique()->values()->all(),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $cards,
                fn (array $card): string => sprintf('Retrieved archive card %d: %s', $card['card'], $card['technology_segment'])
            ),
        ];
    }

    /**
     * Pick retrieval mode from archive status.
     *
     * @param  array{archive_status: string}  $entry
     */
    private function retrievalModeFor(array $entry): string
    {
        return $entry['archive_status'] === 'archived'
            ? 'portfolio-ready'
            : 'refresh-before-use';
    }

    /**
     * Build search keys for retrieving an archive entry.
     *
     * @param  array{technology_segment: string, route_name: string, retrieval_tags: array<int, string>}  $entry
     * @return array<int, string>
     */
    private function searchKeysFor(array $entry): array
    {
        return array_values(array_unique([
            $entry['technology_segment'],
            $entry['route_name'],
            ...$entry['retrieval_tags'],
        ]));
    }

    /**
     * Build a retrieval prompt for one archive entry.
     *
     * @param  array{technology_segment: string, route_name: string}  $entry
     */
    private function retrievalPromptFor(array $entry): string
    {
        return sprintf(
            'Find one archived proof for %s on `%s`, then explain how it proves practical Laravel skill.',
            $entry['technology_segment'],
            $entry['route_name'],
        );
    }

    /**
     * Build reuse targets for one archive entry.
     *
     * @param  array{next_reference: string}  $entry
     * @return array<int, string>
     */
    private function reuseTargetsFor(array $entry): array
    {
        return [
            'Portfolio implementation note.',
            'Interview defense answer.',
            'Next practice session warmup.',
            $entry['next_reference'],
        ];
    }

    /**
     * Build refresh check before reusing the entry.
     *
     * @param  array{command: string, blocker_status: array<string, mixed>}  $entry
     * @return array<string, mixed>
     */
    private function refreshCheckFor(array $entry): array
    {
        return [
            'command' => $entry['command'],
            'blocker_status' => $entry['blocker_status']['status'],
            'required_before_reuse' => $entry['blocker_status']['status'] === 'cleared'
                ? 'Rerun command only if the related route changed.'
                : 'Resolve blocker and rerun command before reuse.',
        ];
    }
}
