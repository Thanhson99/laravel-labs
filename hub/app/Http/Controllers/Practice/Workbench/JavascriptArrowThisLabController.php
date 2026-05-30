<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class JavascriptArrowThisLabController extends Controller
{
    /**
     * Show the runnable JavaScript arrow-function this lab.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.javascript-arrow-this-lab');
    }
}
