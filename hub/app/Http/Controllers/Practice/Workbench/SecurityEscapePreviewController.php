<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class SecurityEscapePreviewController extends Controller
{
    /**
     * Show a runnable Blade escaping and XSS safety workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.security-escape-preview');
    }
}
