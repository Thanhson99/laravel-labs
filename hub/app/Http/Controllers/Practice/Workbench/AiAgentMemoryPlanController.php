<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Show the AI agent memory planning workbench.
 */
final class AiAgentMemoryPlanController extends Controller
{
    /**
     * Show a runnable AI agent memory planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.ai-agent-memory-plan');
    }
}
