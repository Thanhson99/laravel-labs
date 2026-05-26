<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class PracticeTopicIntakeController extends Controller
{
    /**
     * Show a runnable API validation workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.topic-intake', [
            'tracks' => [
                'php-foundation' => 'PHP Foundation',
                'laravel-http' => 'Laravel HTTP',
                'api-validation' => 'API + Validation',
                'testing-quality' => 'Testing + Quality',
                'docker-runtime' => 'Docker + Runtime',
            ],
        ]);
    }
}
