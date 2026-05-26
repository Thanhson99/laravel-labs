<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class EventListenerPlanController extends Controller
{
    /**
     * Show a runnable event/listener planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.event-listener-plan');
    }
}
