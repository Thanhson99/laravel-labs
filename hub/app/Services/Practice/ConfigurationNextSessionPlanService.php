<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationNextSessionPlanService
{
    /**
     * Build the next study session from the configuration handoff packet.
     */
    public function __construct(
        private readonly ConfigurationHandoffPacketService $handoffPacket,
    ) {}

    /**
     * Build a concrete next-session plan for configuration practice.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $packet = $this->handoffPacket->build();

        return [
            'title' => 'Configuration Next Session Plan',
            'summary' => 'Convert the configuration handoff packet into a focused next session with preflight checks, timed drills, deliverables, and stop criteria.',
            'archive_id' => $packet['archive_id'],
            'session_status' => $packet['handoff_status'] === 'ready-to-handoff' ? 'ready' : 'blocked',
            'session_goal' => 'Rehearse the approved configuration evidence and apply it to one fresh review, security review, or interview prompt.',
            'preflight' => [
                'Open the handoff packet and confirm quality-gate status is ready.',
                'Read the portfolio headline once without editing it.',
                'Choose one channel: portfolio, security review, interview, or code review.',
            ],
            'practice_blocks' => [
                [
                    'minutes' => 10,
                    'focus' => 'Recall',
                    'task' => 'Answer one rehearsal prompt from memory before opening supporting pages.',
                ],
                [
                    'minutes' => 20,
                    'focus' => 'Apply',
                    'task' => 'Use one required evidence item to write or speak the selected channel output, including a Security Misconfiguration blocker when security review is selected.',
                ],
                [
                    'minutes' => 10,
                    'focus' => 'Verify',
                    'task' => 'Run the focused tests and compare the output against the publication checklist.',
                ],
            ],
            'deliverables' => [
                'One channel-specific answer or paragraph.',
                'One Security Misconfiguration release-blocker proof when the selected channel is security review.',
                'One cited source key or route from the handoff packet.',
                'One command result proving the configuration pipeline remains ready.',
            ],
            'stop_criteria' => [
                'The learner cannot explain the incident recovery path without reading the postmortem.',
                'The selected output has a claim without proof.',
                'The security review output names a risk but no fail-closed smoke check.',
                'Any focused verification command fails.',
            ],
            'commands' => [
                ...$packet['commands'],
                'php artisan test --filter ConfigurationNextSessionPlanTest',
            ],
            'quality_gate' => $packet['quality_gate'],
        ];
    }
}
