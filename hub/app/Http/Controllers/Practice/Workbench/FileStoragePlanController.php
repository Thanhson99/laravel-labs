<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class FileStoragePlanController extends Controller
{
    /**
     * Show a runnable file storage planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.file-storage-plan');
    }
}
