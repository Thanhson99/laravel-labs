<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\LearningContentRepositoryInterface;
use App\Repositories\Json\JsonLearningContentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            LearningContentRepositoryInterface::class,
            fn (): JsonLearningContentRepository => new JsonLearningContentRepository((string) config('labs.content_path')),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
