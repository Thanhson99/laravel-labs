<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologySkillAssessmentService
{
    /**
     * Create skill assessments from technology interview packs.
     */
    public function __construct(
        private readonly TechnologyInterviewPackService $interviewPacks,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a scored self-assessment for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $pack = $this->interviewPacks->build($technology, $filters);
        $rubric = $this->rubricFor($technology, $pack);

        return [
            'title' => sprintf('Technology Skill Assessment: %s', $technology),
            'technology' => $technology,
            'interview_pack' => $pack,
            'rubric' => $rubric,
            'max_score' => 100,
            'pass_score' => 80,
            'readiness' => [
                'ready_signal' => 'You can explain the source record, changed files, test evidence, and Laravel layer placement without reading the page.',
                'repeat_signal' => 'Repeat the implementation lab when you cannot cite concrete files, commands, or design tradeoffs.',
            ],
            'improvement_tasks' => $this->improvementTasks($technology),
            'progress_payload' => $this->progressPayload->fromRows(
                $rubric,
                fn (array $row): string => $row['criterion'],
            ),
        ];
    }

    /**
     * Build a 100-point rubric from the interview pack.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function rubricFor(string $technology, array $pack): array
    {
        return [
            [
                'criterion' => 'Source understanding',
                'points' => 20,
                'evidence' => $pack['evidence_to_cite'][4] ?? 'Source records are listed in the portfolio artifact.',
                'pass_signal' => sprintf('Explains why the selected JSON records map to `%s`.', $technology),
            ],
            [
                'criterion' => 'Laravel layer placement',
                'points' => 25,
                'evidence' => 'Answer outline explains route, controller, service, and test responsibilities.',
                'pass_signal' => 'Can point to the layer that owns validation, orchestration, business behavior, and verification.',
            ],
            [
                'criterion' => 'Implementation evidence',
                'points' => 20,
                'evidence' => $pack['evidence_to_cite'][2] ?? 'Changed files are listed.',
                'pass_signal' => 'Changed files match the selected technology and avoid content-only fake progress.',
            ],
            [
                'criterion' => 'Verification discipline',
                'points' => 20,
                'evidence' => $pack['evidence_to_cite'][3] ?? 'Verification commands are listed.',
                'pass_signal' => 'Can rerun focused tests and explain what each command proves.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise oral summary with source, implementation, and evidence.',
            ],
        ];
    }

    /**
     * Build next improvement tasks for weak assessment areas.
     *
     * @return array<int, string>
     */
    private function improvementTasks(string $technology): array
    {
        return [
            sprintf('Open the `%s` implementation lab and redo one source record without looking at the snippet first.', $technology),
            'Add one missing failure-path test to the generated example.',
            'Explain why each changed file belongs in its Laravel layer.',
            'Rerun verification commands and save the evidence in the portfolio artifact.',
            'Practice a two-minute spoken explanation using the interview pack.',
        ];
    }
}
