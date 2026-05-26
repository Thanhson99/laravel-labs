<?php

declare(strict_types=1);

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// Load learner-facing hub pages without session middleware for static-style practice pages.
Route::withoutMiddleware([
    PreventRequestForgery::class,
    StartSession::class,
    ShareErrorsFromSession::class,
])->group(function (): void {
    // Load learning-reference pages first because the dashboard owns the root URL.
    require __DIR__.'/web/learning.php';

    // Load source-to-code practice pages before generated lab pages.
    require __DIR__.'/web/practice-content.php';

    // Load generated practice lab pages that compose longer workflows.
    require __DIR__.'/web/practice-labs.php';

    // Load runnable workbench pages backed by native Laravel code.
    require __DIR__.'/web/workbench.php';

    // Keep generic practice routes last so explicit practice pages win.
    require __DIR__.'/web/practice-catalog.php';
});
