<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class RagStrategyPlanController extends Controller
{
    /**
     * Show a runnable RAG strategy planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.rag-strategy-plan');
    }
}
