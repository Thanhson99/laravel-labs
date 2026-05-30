<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeDemoScriptLabService
{
    /**
     * Build demo scripts from weekly practice reports.
     */
    public function __construct(private readonly PracticeWeeklyReportLabService $reports) {}

    /**
     * Create a demo script for presenting one content-backed practice week.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $report = $this->reports->build($filters);
        $dailyOutputs = collect($report['daily_outputs']);

        return [
            'title' => 'Laravel practice demo script',
            'weekly_report' => [
                'title' => $report['title'],
                'day_count' => $report['rotation']['day_count'],
                'technology_coverage' => $report['technology_coverage'],
            ],
            'demo_goal' => 'Present one week of Laravel practice as a repeatable code demo with commands, evidence, and next actions.',
            'script_steps' => $dailyOutputs
                ->map(fn (array $output): array => [
                    'segment' => sprintf('Day %d demo: %s', $output['day'], $output['technology']),
                    'say' => $this->speakerNoteFor($output),
                    'do' => $this->actionFor($output),
                    'verify' => $this->verificationFor($output),
                    'evidence' => $output['evidence_to_attach'],
                ])
                ->all(),
            'opening_lines' => $this->openingLinesFor($report['technology_coverage']),
            'closing_lines' => $this->closingLinesFor($report['technology_coverage']),
            'rehearsal_checklist' => $this->rehearsalChecklistFor($report['technology_coverage']),
            'handoff_payload' => [
                'items' => collect($report['progress_payload']['items'])
                    ->map(fn (array $item): array => [
                        'label' => sprintf('Demo ready: %s', $item['label']),
                        'done' => false,
                    ])
                    ->all(),
            ],
        ];
    }

    /**
     * Create a concise speaker note for one daily output.
     *
     * @param  array{day: int, technology: string, focus: string, output: string, evidence_to_attach: string}  $output
     */
    private function speakerNoteFor(array $output): string
    {
        return sprintf(
            'I practiced `%s` by doing `%s`, then turned it into `%s`.',
            $output['technology'],
            $output['focus'],
            $output['output'],
        );
    }

    /**
     * Return the concrete demo action for one focus.
     *
     * @param  array{focus: string}  $output
     */
    private function actionFor(array $output): string
    {
        if ($output['technology'] === 'idor-access-control') {
            return match ($output['focus']) {
                'Build capstone task' => 'Open the capstone workspace, show the IDOR source record, then map the protected object route, id parameter, owner scope, and policy check.',
                'Run checkpoint exam' => 'Open the checkpoint exam, answer the warmup, then show the attacker ID-swap denial path and status-code decision.',
                'Answer mentor feedback' => 'Open the mentor feedback lab and show the before/after object-authorization answer.',
                'Package portfolio evidence' => 'Open the portfolio lab and show the IDOR route inventory, scoped lookup, policy, denial test, and verification evidence.',
                default => 'Open the retrospective lab and select one concrete object-authorization improvement.',
            };
        }

        if ($output['technology'] === 'javascript-closures') {
            return match ($output['focus']) {
                'Build capstone task' => 'Open the capstone workspace, show the closure source record, and explain the lexical-scope trace.',
                'Run checkpoint exam' => 'Open the checkpoint exam, answer the warmup, then show the captured binding and stale-closure evidence.',
                'Answer mentor feedback' => 'Open the mentor feedback lab and show the before/after closure interview answer.',
                'Package portfolio evidence' => 'Open the portfolio lab and show the closure skills, source reference, and verification evidence.',
                default => 'Open the retrospective lab and select one concrete closure explanation improvement.',
            };
        }

        return match ($output['focus']) {
            'Build capstone task' => 'Open the capstone workspace, show the target Laravel class, and explain the smallest implementation slice.',
            'Run checkpoint exam' => 'Open the checkpoint exam, answer the warmup, then show the coding task and pass criteria.',
            'Answer mentor feedback' => 'Open the mentor feedback lab and show the before/after change from one action item.',
            'Package portfolio evidence' => 'Open the portfolio lab and show the source reference, skills, and verification evidence.',
            default => 'Open the retrospective lab and select one concrete next improvement.',
        };
    }

    /**
     * Return the verification action for one daily output.
     *
     * @param  array{focus: string}  $output
     */
    private function verificationFor(array $output): string
    {
        if ($output['technology'] === 'idor-access-control') {
            return match ($output['focus']) {
                'Build capstone task' => 'Run the focused IDOR tests and confirm scoped lookup, policy check, and cross-user denial evidence.',
                'Run checkpoint exam' => 'Read the IDOR pass criteria and show which object route, owner scope, and denial-test criteria are complete.',
                'Answer mentor feedback' => 'Show the changed object-authorization evidence and rerun the related lab test.',
                'Package portfolio evidence' => 'Show the IDOR portfolio entry and the linked verification command.',
                default => 'Record the next IDOR route, nested resource, download, or export path to review in the next practice session.',
            };
        }

        if ($output['technology'] === 'javascript-closures') {
            return match ($output['focus']) {
                'Build capstone task' => 'Run the focused closure tests and confirm createCounter() or lexical-scope evidence.',
                'Run checkpoint exam' => 'Read the closure pass criteria and show which scope/trap criteria are complete.',
                'Answer mentor feedback' => 'Show the changed closure evidence and rerun the related lab test.',
                'Package portfolio evidence' => 'Show the closure portfolio entry and the linked verification command.',
                default => 'Record the next closure example to explain in the next practice session.',
            };
        }

        return match ($output['focus']) {
            'Build capstone task' => 'Run the focused feature test and Pint for the changed files.',
            'Run checkpoint exam' => 'Read the pass criteria and show which criteria are complete.',
            'Answer mentor feedback' => 'Show the changed file and rerun the related lab test.',
            'Package portfolio evidence' => 'Show the portfolio entry and the linked verification command.',
            default => 'Record the next command to run in the next practice session.',
        };
    }

    /**
     * Return demo opening lines for the selected technologies.
     *
     * @param  array<int, string>  $technologies
     * @return array<int, string>
     */
    private function openingLinesFor(array $technologies): array
    {
        if ($this->hasIdor($technologies)) {
            return [
                'This demo starts from JSON-backed Laravel Labs content and ends in an IDOR object-authorization artifact.',
                'I will show the source-backed route, object id, scoped lookup, policy check, denial test, and evidence.',
            ];
        }

        if ($this->hasClosure($technologies)) {
            return [
                'This demo starts from JSON-backed Laravel Labs content and ends in a JavaScript closure interview artifact.',
                'I will show the source-backed question, lexical-scope trace, captured binding, verification command, and evidence.',
            ];
        }

        return [
            'This demo starts from JSON-backed Laravel Labs content and ends in working Laravel code.',
            'I will show the source-backed task, the Laravel file I changed, the test command, and the evidence.',
        ];
    }

    /**
     * Return demo closing lines for the selected technologies.
     *
     * @param  array<int, string>  $technologies
     * @return array<int, string>
     */
    private function closingLinesFor(array $technologies): array
    {
        if ($this->hasIdor($technologies)) {
            return [
                'The strongest result is the IDOR explanation that has route inventory, scoped lookup, object policy, denial test, and status-code evidence.',
                'The next improvement is selected from the weakest object route, nested resource, download, export, or 403-versus-404 blocker.',
            ];
        }

        if ($this->hasClosure($technologies)) {
            return [
                'The strongest result is the closure explanation that has source traceability, repeated-call evidence, and trap coverage.',
                'The next improvement is selected from the weakest lexical-scope, var-versus-let, or stale-closure blocker.',
            ];
        }

        return [
            'The strongest result is the implementation that has both source traceability and passing verification.',
            'The next improvement is selected from the weekly blockers and next-week plan.',
        ];
    }

    /**
     * Return rehearsal checklist items for the selected technologies.
     *
     * @param  array<int, string>  $technologies
     * @return array<int, string>
     */
    private function rehearsalChecklistFor(array $technologies): array
    {
        if ($this->hasIdor($technologies)) {
            return [
                'Open the related IDOR access-review workspace before starting the demo.',
                'Keep one terminal ready with the focused IDOR test command.',
                'Show the vulnerable direct lookup before showing the scoped lookup and policy check.',
                'End each segment with the denial evidence that proves a copied object id cannot cross user or tenant boundaries.',
            ];
        }

        if ($this->hasClosure($technologies)) {
            return [
                'Open the related closure workspace before starting the demo.',
                'Keep one terminal ready with the focused closure test command.',
                'Show the closure snippet or payload before showing the rendered result.',
                'End each segment with the lexical-scope or stale-closure evidence that proves the work.',
            ];
        }

        return [
            'Open the related practice workspace before starting the demo.',
            'Keep one terminal ready with the focused test command.',
            'Show the changed Laravel file before showing the browser result.',
            'End each segment with the evidence that proves the work.',
        ];
    }

    /**
     * Determine whether this demo covers JavaScript closures.
     *
     * @param  array<int, string>  $technologies
     */
    private function hasClosure(array $technologies): bool
    {
        return in_array('javascript-closures', $technologies, true);
    }

    /**
     * Determine whether this demo covers IDOR object-authorization practice.
     *
     * @param  array<int, string>  $technologies
     */
    private function hasIdor(array $technologies): bool
    {
        return in_array('idor-access-control', $technologies, true);
    }
}
