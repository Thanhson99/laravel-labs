<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class AiCloudInterviewRubricController extends Controller
{
    /**
     * Show a runnable AI Cloud interview rubric workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.ai-cloud-interview-rubric');
    }
}
