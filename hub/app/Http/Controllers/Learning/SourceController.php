<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\LearningContentRepositoryInterface;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SourceController extends Controller
{
    /**
     * Show one decoded JSON source and its extracted records.
     */
    public function __invoke(string $sourceKey, LearningContentRepositoryInterface $content): View
    {
        $source = $content->findSource($sourceKey);

        if ($source === null) {
            throw new NotFoundHttpException('Learning source not found.');
        }

        return view('learning.source', [
            'source' => $source,
            'questions' => $content->questions(),
        ]);
    }
}
