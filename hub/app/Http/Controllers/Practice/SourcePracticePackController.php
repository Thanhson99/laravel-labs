<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\SourcePracticePackService;
use Illuminate\Contracts\View\View;

final class SourcePracticePackController extends Controller
{
    /**
     * Show one source file as a practice pack.
     */
    public function __invoke(string $sourceKey, SourcePracticePackService $packs): View
    {
        $pack = $packs->build($sourceKey);

        abort_if($pack === null, 404);

        return view('practice.source-pack', [
            'pack' => $pack,
        ]);
    }
}
