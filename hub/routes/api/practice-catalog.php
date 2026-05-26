<?php

declare(strict_types=1);

use App\Http\Controllers\Api\PracticeCatalogApiController;
use Illuminate\Support\Facades\Route;

// Practice catalog API endpoints for listing exercises and resolving one exercise by slug.
// Return the complete practice catalog.
Route::get('/', [PracticeCatalogApiController::class, 'index'])->name('index');

// Return one catalog exercise. Keep this generic lookup last in the practice API family.
Route::get('/{exercise}', [PracticeCatalogApiController::class, 'show'])->name('show');
