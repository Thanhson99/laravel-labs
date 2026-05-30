<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class JavascriptHoistingLabController extends Controller
{
    /**
     * Show the runnable JavaScript hoisting lab.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.javascript-hoisting-lab');
    }
}
