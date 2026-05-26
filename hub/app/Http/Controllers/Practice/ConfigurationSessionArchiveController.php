<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationSessionArchiveService;
use Illuminate\Contracts\View\View;

final class ConfigurationSessionArchiveController extends Controller
{
    /**
     * Show the archive entry for a configuration follow-up session.
     */
    public function __invoke(ConfigurationSessionArchiveService $archive): View
    {
        return view('practice.configuration-session-archive', [
            'archive' => $archive->build(),
        ]);
    }
}
