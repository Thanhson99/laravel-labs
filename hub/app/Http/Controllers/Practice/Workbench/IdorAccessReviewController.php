<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class IdorAccessReviewController extends Controller
{
    /**
     * Show a runnable IDOR object-level authorization review workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.idor-access-review');
    }
}
