<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class ContainerBindingPlanController extends Controller
{
    /**
     * Show a runnable service-container binding workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.container-binding-plan');
    }
}
