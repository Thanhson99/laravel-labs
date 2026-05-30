<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class OopAbstractionDecisionController extends Controller
{
    /**
     * Show a runnable OOP abstraction decision workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.oop-abstraction-decision');
    }
}
