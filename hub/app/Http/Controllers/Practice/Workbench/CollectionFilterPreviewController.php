<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class CollectionFilterPreviewController extends Controller
{
    /**
     * Show a runnable collection filtering and pagination workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.collection-filter-preview');
    }
}
