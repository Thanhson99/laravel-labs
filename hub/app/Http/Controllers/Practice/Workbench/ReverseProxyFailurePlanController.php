<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class ReverseProxyFailurePlanController extends Controller
{
    /**
     * Show the reverse-proxy failure-mode planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.reverse-proxy-failure-plan');
    }
}
