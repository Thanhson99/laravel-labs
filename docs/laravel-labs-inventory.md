# Laravel Labs Inventory

This document records the current state of the standalone Laravel projects in this repository. Read it before editing anything under `breeze/`, `chirper/`, `jetstream/`, or `sail/`.

## High-Level Rule

Each lab folder is an independent Laravel project. Do not assume that framework versions, folder structure, dependencies, tests, or routes are the same across labs.

Observed version split:

- `breeze`, `chirper`, and `jetstream` are Laravel 10-style apps requiring PHP `^8.1`.
- `sail` is a Laravel 11-style app requiring PHP `^8.2`.
- `hub` is a Laravel 13-style aggregate app requiring PHP `^8.3`.

This matters because Laravel 10 apps use older structure with `app/Http/Kernel.php`, `app/Console/Kernel.php`, and several provider classes, while the `sail` Laravel 11 app has a slimmer structure.

## Shared Composer Notes

Common PSR-4 autoload roots:

```json
{
  "App\\": "app/",
  "Database\\Factories\\": "database/factories/",
  "Database\\Seeders\\": "database/seeders/"
}
```

Common dev dependencies across the Laravel 10 labs:

- `fakerphp/faker`
- `laravel/pint`
- `laravel/sail`
- `mockery/mockery`
- `nunomaduro/collision`
- `phpunit/phpunit`
- `spatie/laravel-ignition`

Do not modify `composer.lock` unless Composer was actually run for that specific app.

## `hub/`

### Purpose

Aggregate Laravel application for Laravel Labs. It integrates the static portal JSON files into a server-rendered Laravel experience and acts as the first place to connect learning content, question-bank views, and future study progress.

### Framework And PHP

- PHP: `^8.3`
- Laravel framework: `^13.8`
- Tinker: `^3.0`

### Current Integration

- Reads `../data/**` through `config/labs.php`.
- Uses `App\Repositories\Contracts\LearningContentRepositoryInterface`.
- Uses `App\Repositories\Json\JsonLearningContentRepository`.
- Does not require database access for the dashboard, source browser, or question bank.
- Includes Docker support through `Dockerfile`, `docker-compose.yml`, and `docker/entrypoint.sh`.

### Routes

Route files:

- `routes/web.php`
- `routes/web/learning.php`
- `routes/web/practice-catalog.php`
- `routes/web/practice-content.php`
- `routes/web/practice-labs.php`
- `routes/web/workbench.php`
- `routes/api.php`
- `routes/api/learning.php`
- `routes/api/practice-actions.php`
- `routes/api/practice-catalog.php`
- `routes/api/practice-content.php`
- `routes/api/practice-labs.php`

Important behavior:

- `/` renders the hub dashboard and integration status.
- `/practice` renders the primary native code-first practice workspace.
- `/practice/{exercise}` renders one native practice exercise with tasks, files, commands, and done criteria.
- `/workbench/name-normalizer` renders a runnable PHP foundation workbench backed by native app code.
- `/questions` renders a searchable/filterable question bank.
- `/quiz` renders a randomized practice set from filtered question-bank records.
- `/study-plan` renders a generated source/topic-level study plan.
- `/analytics` renders content coverage, source density, and code snippet analytics.
- `/labs` renders hands-on practice labs generated from content topics.
- `/sources/{sourceKey}` renders one decoded JSON source and extracted records.
- `/api/learning` returns JSON API statistics and links.
- `/api/learning/questions` returns filtered question-bank records.
- `/api/learning/quiz` returns generated practice cards.
- `/api/learning/study-plan` returns generated study-plan modules.
- `/api/learning/analytics` returns content analytics.
- `/api/learning/labs` returns hands-on practice labs.
- `/api/practice` returns the native practice catalog.
- `/api/practice/{exercise}` returns one native practice exercise.
- `/api/practice/name-normalizer` accepts raw names and returns normalized output for API practice.
- `/api/learning/sources` returns JSON source metadata.
- `/api/learning/sources/{sourceKey}` returns one decoded source.

### App Areas

Important files and folders:

- `app/Http/Controllers/Learning/*`
- `app/Http/Controllers/Practice/PracticeController.php`
- `app/Services/Practice/PracticeCatalogService.php`
- `config/practice.php`
- `resources/views/practice/*`
- `app/Practice/Php/NameNormalizer.php`
- `app/Http/Controllers/Practice/Workbench/NameNormalizerController.php`
- `app/Http/Requests/Practice/NormalizeNamesRequest.php`
- `app/Repositories/Contracts/LearningContentRepositoryInterface.php`
- `app/Repositories/Json/JsonLearningContentRepository.php`
- `app/Services/Learning/LearningQuizService.php`
- `app/Services/Learning/LearningStudyPlanService.php`
- `app/Services/Learning/LearningAnalyticsService.php`
- `app/Services/Learning/LearningLabService.php`
- `resources/views/learning/*`
- `config/labs.php`
- `Dockerfile`
- `docker-compose.yml`
- `docker/entrypoint.sh`
- `INTEGRATION_NOTES.md`

### Tests

Current tests:

- `tests/Feature/LearningHubTest.php`
- `tests/Feature/LearningApiTest.php`
- `tests/Feature/ExampleTest.php`
- `tests/Unit/ExampleTest.php`

## `breeze/`

### Purpose

Basic Laravel Breeze authentication scaffold for learning standard auth flows, profile editing, Blade views, and auth tests.

### Framework And PHP

- PHP: `^8.1`
- Laravel framework: `^10.10`
- Sanctum: `^3.3`
- Breeze is in `require-dev`: `laravel/breeze ^1.29`

### Frontend

`package.json`:

- Vite
- Tailwind CSS
- Alpine.js
- Axios
- `@tailwindcss/forms`

Scripts:

```json
{
  "dev": "vite",
  "build": "vite build"
}
```

### Routes

Route files:

- `routes/web.php`
- `routes/auth.php`
- `routes/api.php`
- `routes/channels.php`
- `routes/console.php`

Important behavior:

- `/` returns `welcome`.
- `/dashboard` requires `auth` and `verified`.
- `/profile` edit/update/delete routes require `auth`.
- `routes/auth.php` contains auth scaffold routes.

### App Areas

Important files and folders:

- `app/Http/Controllers/ProfileController.php`
- `app/Http/Requests/ProfileUpdateRequest.php`
- `app/Http/Controllers/Auth/*`
- `app/View/Components/AppLayout.php`
- `app/View/Components/GuestLayout.php`
- `resources/views/auth/*`
- `resources/views/profile/*`
- `resources/views/layouts/*`

### Migrations

Current migration set:

- users table
- password reset tokens table
- failed jobs table
- personal access tokens table

### Tests

Test focus:

- auth
- registration
- password reset
- password update
- email verification
- password confirmation
- profile
- example feature/unit tests

Key folders:

- `tests/Feature/Auth`
- `tests/Feature/ProfileTest.php`
- `tests/Unit/ExampleTest.php`

## `chirper/`

### Purpose

Jetstream-style scaffold intended for CRUD/posting-flow practice, but the actual chirp feature is currently incomplete.

### Framework And PHP

- PHP: `^8.1`
- Laravel framework: `^10.10`
- Jetstream: `^4.3`
- Sanctum: `^3.3`
- Livewire: `^3.0`

### Frontend

`package.json`:

- Vite
- Tailwind CSS
- Axios
- `@tailwindcss/forms`
- `@tailwindcss/typography`

Scripts:

```json
{
  "dev": "vite",
  "build": "vite build"
}
```

### Routes

Route files:

- `routes/web.php`
- `routes/api.php`
- `routes/channels.php`
- `routes/console.php`

Important behavior:

- `/` returns `welcome`.
- `/dashboard` requires `auth:sanctum`, Jetstream auth session middleware, and `verified`.
- API `/user` route is protected by `auth:sanctum`.

### App Areas

Important files and folders:

- `app/Actions/Fortify/*`
- `app/Actions/Jetstream/DeleteUser.php`
- `app/Providers/FortifyServiceProvider.php`
- `app/Providers/JetstreamServiceProvider.php`
- `app/Models/User.php`
- `app/Models/Chirp.php`
- `resources/views/profile/*`
- `resources/views/api/*`
- `resources/views/components/*`

### Migrations

Current migration set:

- users table
- password reset tokens table
- two-factor auth columns
- failed jobs table
- personal access tokens table
- sessions table
- chirps table

Known issue:

- `create_chirps_table` currently creates only `id` and timestamps.
- `Chirp` model has no fillable fields or relationships.
- No chirp CRUD routes/views were observed.

### Tests

Test focus:

- authentication
- registration
- password reset/confirmation/update
- profile information
- two-factor authentication settings
- browser sessions
- API tokens
- account deletion
- example feature/unit tests

No observed chirp CRUD test exists.

## `jetstream/`

### Purpose

Jetstream scaffold with teams. Use this lab for team features, invitations, role/permission thinking, profile security, sessions, API tokens, and larger auth flows.

### Framework And PHP

- PHP: `^8.1`
- Laravel framework: `^10.10`
- Jetstream: `^4.3`
- Sanctum: `^3.3`
- Livewire: `^3.0`

### Frontend

`package.json`:

- Vite
- Tailwind CSS
- Axios
- `@tailwindcss/forms`
- `@tailwindcss/typography`

Scripts:

```json
{
  "dev": "vite",
  "build": "vite build"
}
```

### Routes

Route files:

- `routes/web.php`
- `routes/api.php`
- `routes/channels.php`
- `routes/console.php`

Important behavior:

- `/` returns `welcome`.
- `/dashboard` requires `auth:sanctum`, Jetstream auth session middleware, and `verified`.
- Jetstream/Fortify provide most feature routes through service providers and package routes.

### App Areas

Important files and folders:

- `app/Actions/Fortify/*`
- `app/Actions/Jetstream/AddTeamMember.php`
- `app/Actions/Jetstream/CreateTeam.php`
- `app/Actions/Jetstream/DeleteTeam.php`
- `app/Actions/Jetstream/DeleteUser.php`
- `app/Actions/Jetstream/InviteTeamMember.php`
- `app/Actions/Jetstream/RemoveTeamMember.php`
- `app/Actions/Jetstream/UpdateTeamName.php`
- `app/Models/User.php`
- `app/Models/Team.php`
- `app/Models/Membership.php`
- `app/Models/TeamInvitation.php`
- `app/Policies/TeamPolicy.php`
- `app/Providers/FortifyServiceProvider.php`
- `app/Providers/JetstreamServiceProvider.php`
- `resources/views/teams/*`
- `resources/views/profile/*`
- `resources/views/api/*`

### Migrations

Current migration set:

- users table
- password reset tokens table
- two-factor auth columns
- failed jobs table
- personal access tokens table
- teams table
- team user pivot table
- team invitations table
- sessions table

### Tests

Test focus:

- authentication
- registration
- password reset/confirmation/update
- profile information
- two-factor authentication settings
- browser sessions
- API tokens
- account deletion
- team create/update/delete
- invite team member
- remove team member
- update team member role
- leave team
- example feature/unit tests

## `sail/`

### Purpose

Laravel 11 Sail-oriented app for Docker/environment workflow practice.

### Framework And PHP

- PHP: `^8.2`
- Laravel framework: `^11.9`
- Tinker: `^2.9`

### Frontend

`package.json`:

- Vite
- Axios
- `laravel-vite-plugin`

No Tailwind dependency was observed in this `package.json`.

Scripts:

```json
{
  "dev": "vite",
  "build": "vite build"
}
```

### Routes

Route files:

- `routes/web.php`
- `routes/console.php`

Important behavior:

- `/` returns `welcome`.

### App Areas

Important files and folders:

- `bootstrap/app.php`
- `bootstrap/providers.php`
- `app/Models/User.php`
- `app/Providers/AppServiceProvider.php`
- `app/Http/Controllers/Controller.php`

Because this is Laravel 11-style, do not look for `app/Http/Kernel.php` or `app/Console/Kernel.php` as the primary configuration points.

### Migrations

Current migration set:

- users table
- cache table
- jobs table

### Docker Compose

`docker-compose.yml` defines:

- `laravel.test`
- `mysql`
- `redis`
- `meilisearch`
- `mailpit`
- `selenium`

Observed port mapping:

```text
${APP_PORT:-87}:87
${VITE_PORT:-5173}:${VITE_PORT:-5173}
```

### Tests

Current tests:

- `tests/Feature/ExampleTest.php`
- `tests/Unit/ExampleTest.php`
- `tests/TestCase.php`

## Cross-Lab Editing Rules

- Never apply a Laravel 11 bootstrap/config pattern to the Laravel 10 labs without checking the target app.
- Never apply Jetstream team assumptions to `breeze` or `sail`.
- Never assume `chirper` has complete chirp CRUD just because it has a `Chirp` model.
- Run commands from the target lab folder, not the repository root.
- Do not edit `.env`; use `.env.example` for documented defaults.
- Do not edit `vendor`, `node_modules`, `public/build`, storage runtime files, or generated cache files.
- If a feature touches database behavior, inspect that lab's migrations and tests before editing.
