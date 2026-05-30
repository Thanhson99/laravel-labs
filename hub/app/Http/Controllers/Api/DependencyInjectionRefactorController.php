<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanDependencyInjectionRefactorRequest;
use App\Services\Practice\DependencyInjectionRefactorService;
use Illuminate\Http\JsonResponse;

final class DependencyInjectionRefactorController extends Controller
{
    /**
     * Return a Dependency Injection refactor plan for replacing manual dependencies.
     */
    public function __invoke(PlanDependencyInjectionRefactorRequest $request, DependencyInjectionRefactorService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
