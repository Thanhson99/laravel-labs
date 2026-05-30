<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class CsrfProtectionPlanController extends Controller
{
    public function __invoke(): View
    {
        return view('practice.workbench.csrf-protection-plan');
    }
}
