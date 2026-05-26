<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class RecordPracticeWorkspaceService
{
    /**
     * Create a full practice workspace for one JSON content record.
     */
    public function __construct(private readonly ImplementationStarterKitService $starterKits) {}

    /**
     * Build the complete record-to-code workspace.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $starterKit = $this->starterKits->build($filters);

        if ($starterKit === null) {
            return null;
        }

        $blueprint = $starterKit['checklist']['blueprint'];
        $drill = $blueprint['drill'];

        return [
            'title' => sprintf('Record Workspace: %s', $drill['content']['title']),
            'source' => $drill['source'],
            'content' => $drill['content'],
            'technology' => $drill['technology'],
            'practice' => $drill['practice'],
            'goal' => $drill['goal'],
            'blueprint' => [
                'names' => $blueprint['names'],
                'commands' => $blueprint['commands'],
            ],
            'checklist' => [
                'items' => $starterKit['checklist']['items'],
                'progress_payload' => $starterKit['checklist']['progress_payload'],
                'progress_api' => $starterKit['checklist']['progress_api'],
            ],
            'starter_kit' => [
                'snippets' => $starterKit['snippets'],
                'usage' => $starterKit['usage'],
            ],
            'workflow_links' => [
                'source_json' => $drill['source']['key'],
                'exercise' => $drill['practice']['slug'],
                'progress_api' => $starterKit['checklist']['progress_api'],
            ],
        ];
    }
}
