<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\LearningContentRepositoryInterface;
use App\Services\Practice\PracticeCatalogService;
use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    /**
     * Show the learning-by-building dashboard.
     */
    public function __invoke(LearningContentRepositoryInterface $content, PracticeCatalogService $practice): View
    {
        return view('learning.dashboard', [
            'stats' => $content->statistics(),
            'featuredExercises' => array_slice($practice->exercises(), 0, 5),
            'practiceTracks' => $practice->tracks(),
            'integrations' => $this->integrations(),
            'workflow' => $this->workflow(),
        ]);
    }

    /**
     * Describe the intended study workflow for the hub.
     *
     * @return array<int, array{title: string, body: string, href: string}>
     */
    private function workflow(): array
    {
        return [
            [
                'title' => '1. Pick a technology track',
                'body' => 'Start from native practice exercises written in this Laravel app.',
                'href' => route('practice.index'),
            ],
            [
                'title' => '2. Code inside the project',
                'body' => 'Create controllers, services, requests, routes, tests, Docker config, or plain PHP files.',
                'href' => route('practice.index'),
            ],
            [
                'title' => '3. Verify with tests',
                'body' => 'Run the listed commands, feature tests, and Pint checks for the exercise.',
                'href' => route('practice.index'),
            ],
            [
                'title' => '4. Use JSON as reference only',
                'body' => 'Use question bank and quiz after coding, not as the main project experience.',
                'href' => route('learning.quiz'),
            ],
        ];
    }

    /**
     * List the technologies currently integrated into the hub.
     *
     * @return array<int, array{label: string, status: string, note: string}>
     */
    private function integrations(): array
    {
        return [
            [
                'label' => 'Laravel practice app',
                'status' => 'done',
                'note' => 'The hub is now a code-first Laravel workspace, not only a JSON content viewer.',
            ],
            [
                'label' => 'Native practice catalog',
                'status' => 'done',
                'note' => 'Practice exercises are declared in config/practice.php and rendered by Laravel routes.',
            ],
            [
                'label' => 'Routes and controllers',
                'status' => 'done',
                'note' => 'Practice routes use controllers instead of closure-only demo routes.',
            ],
            [
                'label' => 'Service layer',
                'status' => 'done',
                'note' => 'PracticeCatalogService, quiz, labs, analytics, and study plan services keep controllers thin.',
            ],
            [
                'label' => 'Tests',
                'status' => 'done',
                'note' => 'Feature tests verify practice workspace, generated labs, APIs, and dashboard behavior.',
            ],
            [
                'label' => 'JSON API',
                'status' => 'done',
                'note' => 'Practice and learning APIs are available for hands-on API validation work.',
            ],
            [
                'label' => 'Quiz practice mode',
                'status' => 'done',
                'note' => 'Generates randomized practice cards from JSON content through /quiz and /api/learning/quiz.',
            ],
            [
                'label' => 'Study plan generator',
                'status' => 'done',
                'note' => 'Builds topic-level learning plans from JSON source density through /study-plan and /api/learning/study-plan.',
            ],
            [
                'label' => 'Content analytics',
                'status' => 'done',
                'note' => 'Reports source density, language coverage, record types, and code-heavy topics through /analytics and /api/learning/analytics.',
            ],
            [
                'label' => 'Practice labs',
                'status' => 'done',
                'note' => 'Turns JSON topics into hands-on labs with tasks, files, commands, and done criteria through /labs and /api/learning/labs.',
            ],
            [
                'label' => 'Docker',
                'status' => 'done',
                'note' => 'Runs the hub through hub/docker-compose.yml with PHP, Composer, and SQLite support inside the container.',
            ],
            [
                'label' => 'Database',
                'status' => 'pending',
                'note' => 'Not required yet. Current integration is read-only and file-backed.',
            ],
            [
                'label' => 'Frontend build tooling',
                'status' => 'pending',
                'note' => 'Node is not available on this machine, so the first UI uses plain Blade and CSS.',
            ],
        ];
    }
}
