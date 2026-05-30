<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationArchiveRetrievalService
{
    /**
     * Build retrieval practice from archived configuration evidence.
     */
    public function __construct(
        private readonly ConfigurationEvidenceArchiveService $archive,
    ) {}

    /**
     * Build evidence retrieval drills for configuration practice.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $archive = $this->archive->build();

        return [
            'title' => 'Configuration Archive Retrieval',
            'summary' => 'Practice retrieving the right configuration evidence for portfolio notes, interview answers, reviews, and incident recovery.',
            'archive_id' => $archive['archive_id'],
            'retrieval_cases' => $this->retrievalCases($archive),
            'search_keys' => $archive['retrieval_keys'],
            'reuse_targets' => $archive['reuse_targets'],
            'quality_checks' => [
                'Retrieved proof names the source route or command.',
                'Retrieved proof includes a reason, not only a copied label.',
                'Security Misconfiguration proof names the unsafe signal and the fail-closed smoke check.',
                'Incident recovery proof cites the postmortem id and root cause.',
                'Portfolio or interview reuse keeps the quality-gate status visible.',
            ],
            'commands' => [
                ...$archive['commands'],
                'php artisan test --filter ConfigurationArchiveRetrievalTest',
            ],
            'quality_gate' => $archive['quality_gate'],
        ];
    }

    /**
     * Build concrete retrieval cases from archive content.
     *
     * @param  array<string, mixed>  $archive
     * @return array<int, array<string, mixed>>
     */
    private function retrievalCases(array $archive): array
    {
        return [
            [
                'use_case' => 'Portfolio note',
                'prompt' => 'Find proof that configuration is treated as a runtime contract.',
                'key' => $archive['retrieval_keys'][0],
                'proof' => $archive['proof_bundle'][0]['proof'],
                'reuse_target' => $archive['reuse_targets'][0],
            ],
            [
                'use_case' => 'Security review',
                'prompt' => 'Find proof that Security Misconfiguration release blockers stop unsafe production configuration.',
                'key' => 'security misconfiguration release blockers',
                'proof' => $archive['retrieval_prompts'][1],
                'reuse_target' => $archive['reuse_targets'][1],
            ],
            [
                'use_case' => 'Interview answer',
                'prompt' => 'Find evidence for explaining config(), env(), auth defaults, and quality status.',
                'key' => 'config env contract interview',
                'proof' => $archive['retrieval_prompts'][4],
                'reuse_target' => $archive['reuse_targets'][2],
            ],
            [
                'use_case' => 'Incident recovery',
                'prompt' => 'Find the postmortem root cause and recovery route for configuration drift.',
                'key' => $archive['retrieval_keys'][6],
                'proof' => $archive['incident_archive']['recovery_route'].' -> '.$archive['incident_archive']['root_cause'],
                'reuse_target' => $archive['reuse_targets'][4],
            ],
        ];
    }
}
