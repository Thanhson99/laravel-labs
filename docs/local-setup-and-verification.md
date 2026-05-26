# Local Setup And Verification

This document records how to run and verify this repository on a new machine.

## Expected Tooling

The static portal can be inspected with only a browser and a local HTTP server. It uses `fetch`, so opening HTML files directly from disk is not reliable.

Useful tools:

- Git
- A local HTTP server
- A browser
- PowerShell on Windows
- PHP and Composer for Laravel labs
- Node and npm for Vite/Tailwind assets
- Docker for the `sail` lab

The observed machine on 2026-05-25 did not have:

- `php`
- `composer`
- `node`

Do not assume these commands are available.

## Static Site: Run Locally

Because the static site fetches partials and JSON, serve the repo through HTTP.

If Python is available:

```powershell
python -m http.server 8000
```

Then open:

```text
http://localhost:8000/index.html
```

If Node is available:

```powershell
npx serve .
```

or:

```powershell
npx http-server .
```

Use the port printed by the command.

## Static Site: Smoke Test Pages

Check these representative pages:

- `/index.html`
- `/sites/roadmap/index.html`
- `/sites/php/index.html`
- `/sites/php/starter.html`
- `/sites/laravel/index.html`
- `/sites/laravel/auth-security.html`
- `/sites/interview/index.html`
- `/sites/interview/senior.html`
- `/sites/vibe-coding/index.html`
- `/sites/vibe-coding/ai-review.html`
- `/sites/glossary/index.html`
- `/sites/practice/index.html`

Smoke test checklist:

- Page shell loads.
- Header navigation works.
- Back link points to the portal root.
- Language switch works.
- Theme switch works.
- Breadcrumbs are correct.
- Global search opens and returns results.
- Topic switcher works on Laravel, Interview, and Vibe pages.
- PHP level switcher works.
- PHP keyword jump list works on level pages.
- Roadmap filters work.
- Progress buttons update counts.

## JSON Verification

Validate data JSON only:

```powershell
Get-ChildItem data -Recurse -Filter *.json | ForEach-Object {
  $file = $_.FullName
  try {
    Get-Content -Raw -LiteralPath $file | ConvertFrom-Json | Out-Null
  } catch {
    Write-Output ("DATA JSON FAIL: " + $file + " :: " + $_.Exception.Message)
  }
}
```

Avoid using PowerShell `ConvertFrom-Json` as the only validator for `package-lock.json`. Some package lock files contain an empty string key that can trigger a PowerShell parser error even when the file is valid for npm.

## Link Checks

Find hardcoded root back links:

```powershell
rg -n 'href="../index.html"' partials assets sites index.html
```

Find page wrappers:

```powershell
rg -n "data-page=|data-site-root=" index.html sites
```

Find direct `innerHTML` usage:

```powershell
rg -n "innerHTML" assets\site.js assets\shell.js
```

## Laravel Labs: General Setup

Each Laravel folder is independent. Do not run commands from the repo root unless the command is intentionally cross-repo.

Read `docs/laravel-labs-inventory.md` before running app-specific commands. The labs do not all use the same Laravel version.

Typical setup inside one lab:

```powershell
cd breeze
composer install
copy .env.example .env
php artisan key:generate
npm install
npm run build
php artisan test
```

Adjust for each folder's actual `composer.json`, `package.json`, and environment needs.

## Hub App

Folder:

```text
hub/
```

Useful commands if tooling is installed:

```powershell
cd hub
composer install
$env:SESSION_DRIVER="file"
$env:CACHE_STORE="file"
$env:QUEUE_CONNECTION="sync"
php artisan serve
php artisan test
vendor\bin\pint --test
docker-compose up --build
```

The hub reads portal JSON content from `../data` by default through `config/labs.php`. Override with `LABS_CONTENT_PATH` if the content directory moves.

Known environment notes:

- The current PHP installation can serve and test the read-only hub routes.
- SQLite migrations may fail if the local PHP installation lacks the SQLite PDO driver.
- Node is not currently available on this machine, so Vite/npm verification is blocked.

## Breeze Lab

Folder:

```text
breeze/
```

Useful commands if tooling is installed:

```powershell
cd breeze
composer install
npm install
npm run build
php artisan test
```

Important files:

- `routes/web.php`
- `routes/auth.php`
- `app/Http/Controllers/ProfileController.php`
- `resources/views/auth/*.blade.php`
- `resources/views/profile/*.blade.php`
- `tests/Feature/Auth/*.php`

## Jetstream Lab

Folder:

```text
jetstream/
```

Useful commands:

```powershell
cd jetstream
composer install
npm install
npm run build
php artisan test
```

Important areas:

- `app/Actions/Fortify`
- `app/Actions/Jetstream`
- `app/Models/Team.php`
- `app/Policies/TeamPolicy.php`
- `resources/views/teams`
- `tests/Feature/*Team*.php`

## Chirper Lab

Folder:

```text
chirper/
```

Useful commands:

```powershell
cd chirper
composer install
npm install
npm run build
php artisan test
```

Important note:

- `Chirp` currently appears incomplete. See `docs/technical-debt.md`.

Important files:

- `app/Models/Chirp.php`
- `database/migrations/2024_09_17_021924_create_chirps_table.php`
- `routes/web.php`
- `resources/views/dashboard.blade.php`

## Sail Lab

Folder:

```text
sail/
```

This folder includes `docker-compose.yml`.

The observed compose file defines:

- `laravel.test`
- `mysql`
- `redis`
- `meilisearch`
- `mailpit`
- `selenium`

The app port mapping is:

```text
${APP_PORT:-87}:87
```

Check `.env.example` and `docker-compose.yml` before running Docker commands.

Typical Sail commands, if dependencies are installed:

```powershell
cd sail
composer install
copy .env.example .env
php artisan key:generate
vendor\bin\sail up -d
vendor\bin\sail artisan test
```

On Windows, command syntax may differ depending on shell and Docker setup.

## What To Report If Verification Is Blocked

If a command cannot run because tooling is missing, state it clearly:

- command attempted
- error summary
- what verification was still completed
- what should be run later on a fully provisioned machine
