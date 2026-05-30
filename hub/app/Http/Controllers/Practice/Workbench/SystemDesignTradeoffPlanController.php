<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class SystemDesignTradeoffPlanController extends Controller
{
    /**
     * Show the runnable system-design tradeoff planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.system-design-tradeoff-plan');
    }
}
