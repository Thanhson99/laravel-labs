<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationArchiveRetrievalService;
use Illuminate\Contracts\View\View;

final class ConfigurationArchiveRetrievalController extends Controller
{
    /**
     * Show retrieval drills for archived configuration evidence.
     */
    public function __invoke(ConfigurationArchiveRetrievalService $retrieval): View
    {
        return view('practice.configuration-archive-retrieval', [
            'retrieval' => $retrieval->build(),
        ]);
    }
}
