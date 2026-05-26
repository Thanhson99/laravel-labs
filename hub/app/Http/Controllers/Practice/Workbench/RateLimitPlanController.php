<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class RateLimitPlanController extends Controller
{
    /**
     * Show a runnable rate-limit planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.rate-limit-plan');
    }
}
