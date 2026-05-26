<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\PracticeNoteService;
use Illuminate\Contracts\View\View;

final class NoteController extends Controller
{
    /**
     * Show a practice note backed by a service.
     */
    public function __invoke(PracticeNoteService $notes): View
    {
        return view('practice.note', [
            'note' => $notes->first(),
        ]);
    }
}
