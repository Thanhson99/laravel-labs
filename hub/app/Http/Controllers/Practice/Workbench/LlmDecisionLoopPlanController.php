<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class LlmDecisionLoopPlanController extends Controller
{
    /**
     * Show a runnable LLM decision-loop planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.llm-decision-loop-plan');
    }
}
