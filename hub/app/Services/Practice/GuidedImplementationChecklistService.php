<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class GuidedImplementationChecklistService
{
    /**
     * Create guided checklists from implementation blueprints.
     */
    public function __construct(
        private readonly ContentImplementationBlueprintService $blueprints,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a TDD checklist for one content-backed implementation.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $blueprint = $this->blueprints->build($filters);

        if ($blueprint === null) {
            return null;
        }

        $names = $blueprint['names'];

        $items = [
            $this->item('Read source record', sprintf('Open %s and read: %s', $blueprint['drill']['source']['path'], $blueprint['drill']['content']['title'])),
            $this->item('Write failing test', sprintf('Create %s and assert the expected behavior.', $names['test_file'])),
            $this->item('Add request validation', sprintf('Create or update %s.', $names['request_file'] ?? 'the relevant Form Request')),
            $this->item('Add controller route', sprintf('Register %s %s and call the controller.', $names['route_method'], $names['route_path'])),
            $this->item('Move logic to service', sprintf('Implement %s with the smallest useful behavior.', $names['service_file'])),
            $this->item('Run verification', implode(' | ', $blueprint['commands'])),
        ];

        return [
            'title' => sprintf('Guided Checklist: %s', $blueprint['drill']['content']['title']),
            'blueprint' => $blueprint,
            'items' => $items,
            'progress_payload' => $this->progressPayload->fromRows(
                $items,
                fn (array $item): string => $item['label']
            ),
            'progress_api' => '/api/practice/progress-checklist',
        ];
    }

    /**
     * Create one checklist item.
     *
     * @return array{label: string, detail: string, done: bool}
     */
    private function item(string $label, string $detail): array
    {
        return [
            'label' => $label,
            'detail' => $detail,
            'done' => false,
        ];
    }
}
