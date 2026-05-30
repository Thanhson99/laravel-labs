<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class JwtRevocationPlanController extends Controller
{
    /**
     * Show a runnable JWT revocation planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.jwt-revocation-plan');
    }
}
