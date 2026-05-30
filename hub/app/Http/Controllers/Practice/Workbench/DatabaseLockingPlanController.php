<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Renders the database-locking planning workbench.
 */
final class DatabaseLockingPlanController extends Controller
{
    /**
     * Show a runnable database-locking planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.database-locking-plan');
    }
}
