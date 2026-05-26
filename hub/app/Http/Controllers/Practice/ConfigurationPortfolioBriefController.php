<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationPortfolioBriefService;
use Illuminate\Contracts\View\View;

final class ConfigurationPortfolioBriefController extends Controller
{
    /**
     * Show the portfolio brief for configuration evidence.
     */
    public function __invoke(ConfigurationPortfolioBriefService $brief): View
    {
        return view('practice.configuration-portfolio-brief', [
            'brief' => $brief->build(),
        ]);
    }
}
