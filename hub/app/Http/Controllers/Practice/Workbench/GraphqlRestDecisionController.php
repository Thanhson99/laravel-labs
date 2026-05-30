<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class GraphqlRestDecisionController extends Controller
{
    /**
     * Show the runnable REST versus GraphQL planning workbench.
     */
    public function __invoke(): View
    {
        return view('practice.workbench.graphql-rest-decision');
    }
}
