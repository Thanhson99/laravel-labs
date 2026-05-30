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

        $items = $this->itemsFor($blueprint, $names);

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

    /**
     * Build checklist items for one implementation blueprint.
     *
     * @param  array<string, mixed>  $blueprint
     * @param  array<string, string>  $names
     * @return array<int, array{label: string, detail: string, done: bool}>
     */
    private function itemsFor(array $blueprint, array $names): array
    {
        if ($blueprint['drill']['technology'] === 'idor-access-control') {
            return [
                $this->item('Read IDOR source record', sprintf('Open %s and read: %s', $blueprint['drill']['source']['path'], $blueprint['drill']['content']['title'])),
                $this->item('Write failing ID-swap test', sprintf('Create %s and assert a second user or tenant cannot replay another object id.', $names['test_file'])),
                $this->item('Inventory object surface', 'List route parameter, object type, owner or tenant boundary, nested resources, downloads, exports, and allowed actions before implementing.'),
                $this->item('Add scoped lookup evidence', 'Replace direct model lookup with current user, tenant, organization, team, or parent-resource scoped lookup.'),
                $this->item('Add object authorization check', sprintf('Implement %s with policy, Gate, FormRequest authorize(), or service-level object authorization evidence.', $names['service_file'])),
                $this->item('Run IDOR verification', implode(' | ', $blueprint['commands'])),
            ];
        }

        if ($this->isArrowThisBlueprint($blueprint)) {
            return [
                $this->item('Read arrow-this source record', sprintf('Open %s and read: %s', $blueprint['drill']['source']['path'], $blueprint['drill']['content']['title'])),
                $this->item('Write failing arrow-this test', sprintf('Create %s and assert lexical `this`, dynamic `this`, object-method trap, and bind trap evidence.', $names['test_file'])),
                $this->item('Define this-binding evidence', 'List the arrow creation scope, normal method call site, callback use case, and call/apply/bind limitation before implementing.'),
                $this->item('Add arrow-this evidence route', sprintf('Register %s %s and call the controller.', $names['route_method'], $names['route_path'])),
                $this->item('Move this-binding logic to service', sprintf('Implement %s with the smallest useful arrow-this comparison payload.', $names['service_file'])),
                $this->item('Run arrow-this verification', implode(' | ', $blueprint['commands'])),
            ];
        }

        if ($blueprint['drill']['technology'] === 'javascript-closures') {
            return [
                $this->item('Read closure source record', sprintf('Open %s and read: %s', $blueprint['drill']['source']['path'], $blueprint['drill']['content']['title'])),
                $this->item('Write failing closure test', sprintf('Create %s and assert lexical scope, captured binding, and stale-closure evidence.', $names['test_file'])),
                $this->item('Define evidence payload', 'List the closure definition, createCounter() trace, practical uses, and interview traps before implementing.'),
                $this->item('Add closure evidence route', sprintf('Register %s %s and call the controller.', $names['route_method'], $names['route_path'])),
                $this->item('Move closure logic to service', sprintf('Implement %s with the smallest useful closure evidence.', $names['service_file'])),
                $this->item('Run closure verification', implode(' | ', $blueprint['commands'])),
            ];
        }

        return [
            $this->item('Read source record', sprintf('Open %s and read: %s', $blueprint['drill']['source']['path'], $blueprint['drill']['content']['title'])),
            $this->item('Write failing test', sprintf('Create %s and assert the expected behavior.', $names['test_file'])),
            $this->item('Add request validation', sprintf('Create or update %s.', $names['request_file'] ?? 'the relevant Form Request')),
            $this->item('Add controller route', sprintf('Register %s %s and call the controller.', $names['route_method'], $names['route_path'])),
            $this->item('Move logic to service', sprintf('Implement %s with the smallest useful behavior.', $names['service_file'])),
            $this->item('Run verification', implode(' | ', $blueprint['commands'])),
        ];
    }

    /**
     * Detect arrow-function `this` content inside a JavaScript closure blueprint.
     *
     * @param  array<string, mixed>  $blueprint
     */
    private function isArrowThisBlueprint(array $blueprint): bool
    {
        if (($blueprint['drill']['technology'] ?? null) !== 'javascript-closures') {
            return false;
        }

        $haystack = strtolower(implode(' ', [
            $blueprint['drill']['content']['title'] ?? '',
            $blueprint['drill']['content']['body'] ?? '',
            $blueprint['drill']['goal'] ?? '',
        ]));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }
}
