<?php

declare(strict_types=1);

use App\Http\Controllers\Practice\NoteController;
use App\Http\Controllers\Practice\PracticeController;
use App\Http\Controllers\Practice\PracticeSessionController;
use Illuminate\Support\Facades\Route;

// Primary practice catalog, session helper, notes, and generic exercise detail.
// Show the main practice catalog.
Route::get('/practice', [PracticeController::class, 'index'])->name('practice.index');

// Show today's generated practice session plan.
Route::get('/practice-sessions/today', PracticeSessionController::class)->name('practice.sessions.today');

// Show the thin-controller practice note page.
Route::get('/practice-notes/thin-controller', NoteController::class)->name('practice.notes.show');

// Show one catalog exercise. Keep this generic route after explicit practice pages.
Route::get('/practice/{exercise}', [PracticeController::class, 'show'])->name('practice.show');
