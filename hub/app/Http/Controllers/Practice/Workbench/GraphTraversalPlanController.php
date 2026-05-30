<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Renders the BFS/DFS traversal planning workbench.
 */
final class GraphTraversalPlanController extends Controller
{
    /**
     * Show a runnable BFS/DFS traversal planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.graph-traversal-plan');
    }
}
