<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationHandoffPacketService
{
    /**
     * Build handoff guidance from the publication checklist.
     */
    public function __construct(
        private readonly ConfigurationPublicationChecklistService $publicationChecklist,
    ) {}

    /**
     * Build the final handoff packet for configuration practice.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $checklist = $this->publicationChecklist->build();

        return [
            'title' => 'Configuration Handoff Packet',
            'summary' => 'Package the approved configuration portfolio artifact into a concise handoff for the next study session, interview rehearsal, or code review.',
            'archive_id' => $checklist['archive_id'],
            'handoff_status' => $checklist['publication_status'] === 'approved' ? 'ready-to-handoff' : 'blocked',
            'handoff_summary' => [
                'Configuration evidence is approved with '.$checklist['score'].' points.',
                'Publish channels are ready for portfolio, security review, interview, and code review reuse.',
                'Quality-gate status remains '.$checklist['quality_gate']['status'].'.',
            ],
            'packet_links' => [
                ['label' => 'Publication checklist', 'route' => '/practice/configuration-publication-checklist'],
                ['label' => 'Portfolio review', 'route' => '/practice/configuration-portfolio-review'],
                ['label' => 'Portfolio brief', 'route' => '/practice/configuration-portfolio-brief'],
                ['label' => 'Evidence archive', 'route' => '/practice/configuration-evidence-archive'],
            ],
            'required_evidence' => collect($checklist['channels'])
                ->map(fn (array $channel): array => [
                    'channel' => $channel['name'],
                    'proof' => $channel['proof_required'],
                ])
                ->values()
                ->all(),
            'rehearsal_prompts' => [
                'Explain why app/auth configuration was treated as a runtime contract.',
                'Explain which Security Misconfiguration release blocker would stop deployment and which smoke check proves it.',
                'Name the command that proves route documentation still protects learner-facing pages.',
                'Defend the incident recovery path without rereading the postmortem.',
            ],
            'next_actions' => [
                'Open the portfolio brief and rehearse the headline once.',
                'Open the Security review channel and attach one fail-closed smoke-check proof.',
                'Use the interview channel proof to answer one mock interview question.',
                'Carry the code-review channel proof into the next configuration PR.',
            ],
            'commands' => [
                ...$checklist['commands'],
                'php artisan test --filter ConfigurationHandoffPacketTest',
            ],
            'quality_gate' => $checklist['quality_gate'],
        ];
    }
}
