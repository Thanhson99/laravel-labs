<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class KubernetesAnalogyPlanController extends Controller
{
    /**
     * Show a runnable Kubernetes analogy workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.kubernetes-analogy-plan');
    }
}
