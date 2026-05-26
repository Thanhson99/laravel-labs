<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyInterviewPackService
{
    /**
     * Create interview defense packs from technology portfolio artifacts.
     */
    public function __construct(
        private readonly TechnologyPortfolioArtifactService $artifacts,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build interview questions, answer outlines, and evidence for one technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $artifact = $this->artifacts->build($technology, $filters);

        return [
            'title' => sprintf('Technology Interview Pack: %s', $technology),
            'technology' => $technology,
            'artifact' => $artifact,
            'questions' => $this->questionsFor($technology, $artifact),
            'evidence_to_cite' => $this->evidenceToCite($artifact),
            'practice_script' => $this->practiceScript($technology, $artifact),
            'progress_payload' => $this->progressPayload->fromLabels([
                'Answer architecture question',
                'Answer implementation question',
                'Answer testing question',
                'Cite changed files and verification',
                'Practice concise oral summary',
            ]),
        ];
    }

    /**
     * Build interview questions with answer outlines.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function questionsFor(string $technology, array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected source record');

        return [
            [
                'question' => sprintf('How did you turn `%s` learning content into Laravel code?', $technology),
                'answer_outline' => [
                    sprintf('I started from the JSON source record `%s`.', $sampleTitle),
                    'I mapped the content to a technology-specific practice task.',
                    'I implemented code in Laravel files instead of changing the source JSON.',
                    'I verified the behavior with focused commands.',
                ],
            ],
            [
                'question' => 'Which Laravel layers did you touch and why?',
                'answer_outline' => [
                    'Routes expose the URL or API entry point only.',
                    'Controllers orchestrate requests and delegate behavior.',
                    'Services contain the implementation logic for the practice task.',
                    'Tests and verification commands prove the flow works.',
                ],
            ],
            [
                'question' => 'How would you review this work before merging it?',
                'answer_outline' => [
                    'Check that changed files match the technology and source records.',
                    'Confirm validation, authorization, storage, queue, cache, or view responsibilities are in the right layer.',
                    'Run the listed verification commands and capture evidence.',
                    'Use the review checklist from the commit plan.',
                ],
            ],
        ];
    }

    /**
     * Build concrete evidence references the learner can cite.
     *
     * @return array<int, string>
     */
    private function evidenceToCite(array $artifact): array
    {
        return [
            sprintf('Branch: %s', $artifact['commit_plan']['branch']),
            sprintf('Commit message: %s', $artifact['commit_plan']['commit_message']),
            sprintf('Changed files: %s', implode(', ', $artifact['portfolio']['changed_files'])),
            sprintf('Verification: %s', implode(' | ', $artifact['portfolio']['verification'])),
            sprintf('Source records covered: %d', count($artifact['portfolio']['source_coverage'])),
        ];
    }

    /**
     * Build a short oral practice script.
     *
     * @return array<int, string>
     */
    private function practiceScript(string $technology, array $artifact): array
    {
        return [
            sprintf('I practiced `%s` by mapping JSON learning records into Laravel implementation tasks.', $technology),
            sprintf('The work is traceable through `%s` and the changed files listed in the artifact.', $artifact['commit_plan']['branch']),
            'I can explain the source record, the Laravel layer placement, the tests, and the verification evidence.',
        ];
    }
}
