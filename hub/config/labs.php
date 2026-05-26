<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Labs Content Path
    |--------------------------------------------------------------------------
    |
    | The hub app reads the existing static portal JSON files directly so the
    | Laravel integration stays aligned with the source learning content.
    |
    */
    'content_path' => env('LABS_CONTENT_PATH', base_path('../data')),

    /*
    |--------------------------------------------------------------------------
    | Hub Runtime URL
    |--------------------------------------------------------------------------
    |
    | Used by generated practice commands that need to call the running hub
    | from a browser or terminal. Keep environment-specific hosts and ports in
    | the .env file instead of hardcoding them in learning content.
    |
    */
    'runtime_base_url' => env('HUB_RUNTIME_BASE_URL', 'http://localhost:8088'),

    /*
    |--------------------------------------------------------------------------
    | Configuration Practice Identifiers
    |--------------------------------------------------------------------------
    |
    | These IDs are used across the configuration practice pipeline as durable
    | learning artifact keys. They are configurable so teams can avoid leaking
    | internal naming conventions or environment-specific identifiers.
    |
    */
    'configuration' => [
        'archive_prefix' => env('CONFIGURATION_ARCHIVE_PREFIX', 'configuration-contract'),
        'session_archive_suffix' => env('CONFIGURATION_SESSION_ARCHIVE_SUFFIX', 'session-debrief'),
        'incident_root_cause' => env('CONFIGURATION_INCIDENT_ROOT_CAUSE', 'A configuration contract changed without updating every dependent readiness and evidence artifact.'),
        'ids' => [
            'decision_record' => env('CONFIGURATION_DECISION_RECORD_ID', 'ADR-CONFIG-001'),
            'runbook' => env('CONFIGURATION_RUNBOOK_ID', 'RUNBOOK-CONFIG-001'),
            'incident' => env('CONFIGURATION_INCIDENT_ID', 'INC-CONFIG-001'),
            'postmortem' => env('CONFIGURATION_POSTMORTEM_ID', 'POSTMORTEM-CONFIG-001'),
        ],
    ],
];
