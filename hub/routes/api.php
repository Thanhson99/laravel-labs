<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Load practice and learning API route groups for the hub app.
Route::prefix('practice')->name('api.practice.')->group(function (): void {
    // Load source-backed practice API endpoints before generated lab endpoints.
    require __DIR__.'/api/practice-content.php';

    // Load generated lab API endpoints that compose practice workflows.
    require __DIR__.'/api/practice-labs.php';

    // Load action API endpoints that accept learner input or runtime checks.
    require __DIR__.'/api/practice-actions.php';

    // Keep catalog and generic exercise lookup last so explicit API routes win.
    require __DIR__.'/api/practice-catalog.php';
});

// Load learning-reference APIs under the shared /api/learning namespace.
Route::prefix('learning')->name('api.learning.')->group(function (): void {
    // Load generated learning-content API endpoints.
    require __DIR__.'/api/learning.php';
});
