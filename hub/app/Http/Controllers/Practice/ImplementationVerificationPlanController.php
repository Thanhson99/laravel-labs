<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\ImplementationVerificationPlanService;
use Illuminate\Contracts\View\View;

final class ImplementationVerificationPlanController extends Controller
{
    /**
     * Show verification commands for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, ImplementationVerificationPlanService $plans): View
    {
        $filters = $request->contentFilters();

        return view('practice.verification-plan', [
            'plan' => $plans->build($filters),
            'filters' => $filters,
        ]);
    }
}
