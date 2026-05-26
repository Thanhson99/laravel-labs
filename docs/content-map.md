# Content Map

This file maps the static portal, JSON content, shells, and Laravel lab folders. Read it before adding pages, topics, links, or data.

## Static Portal Entry Points

The root static portal is the main published experience.

For content purpose and learning-area boundaries, read `docs/content-taxonomy.md`. For UI/CSS rules, read `docs/design-system.md`.

- Root page: `index.html`
- Shared scripts: `assets/shell.js`, `assets/site.js`
- Shared CSS entry: `assets/styles.css`
- CSS modules:
  - `assets/css/01-core.css`
  - `assets/css/02-roadmap-and-layout.css`
  - `assets/css/03-php-and-learning.css`
  - `assets/css/04-content-and-components.css`
  - `assets/css/05-overrides-and-responsive.css`

The live site referenced by the README is:

- `https://thanhson99.github.io/laravel-labs/index.html`

## Runtime Flow

Every static page is a thin HTML wrapper.

1. The wrapper sets `data-page`.
2. The wrapper sets `data-site-root`.
3. The wrapper loads `assets/shell.js`.
4. `shell.js` chooses one partial from `partials/`.
5. The wrapper loads `assets/site.js`.
6. `site.js` waits for `window.__shellReady`.
7. `site.js` loads `data/site-content.{language}.json`.
8. `site.js` loads topic JSON when needed.
9. Renderer functions fill the shell placeholders.

Do not put page-specific content directly in `sites/**/*.html` unless there is a strong reason. Put content in JSON and rendering behavior in `assets/site.js`.

## Shell Map

`assets/shell.js` resolves shells as follows:

- `landing` -> `partials/landing-shell.html`
- `roadmap` -> `partials/detail-shell.html`
- `practice` -> `partials/detail-shell.html`
- `glossary` -> `partials/detail-shell.html`
- `laravel` -> `partials/topic-shell.html`
- `interview` -> `partials/topic-shell.html`
- `vibe-coding` -> `partials/topic-shell.html`
- `php` -> `partials/php-overview-shell.html`
- `laravel-*` -> `partials/topic-shell.html`
- `interview-*` -> `partials/topic-shell.html`
- `vibe-*` -> `partials/topic-shell.html`
- `php-*` -> `partials/php-level-shell.html`
- fallback -> `partials/detail-shell.html`

Partial placeholders are tightly coupled to renderer functions. If a placeholder ID is removed from a partial, check `assets/site.js` for `document.getElementById(...)` calls that depend on it.

## Page Keys And URL Map

URLs are centralized in `PAGE_PATHS` in `assets/site.js`.

Core pages:

- `landing` -> `index.html`
- `roadmap` -> `sites/roadmap/index.html`
- `glossary` -> `sites/glossary/index.html`
- `practice` -> `sites/practice/index.html`

PHP pages:

- `php` -> `sites/php/index.html`
- `php-starter` -> `sites/php/starter.html`
- `php-intermediate` -> `sites/php/intermediate.html`
- `php-advanced` -> `sites/php/advanced.html`

Laravel pages:

- `laravel` -> `sites/laravel/index.html`
- `laravel-overview` -> `sites/laravel/overview.html`
- `laravel-structure` -> `sites/laravel/structure.html`
- `laravel-composer` -> `sites/laravel/composer.html`
- `laravel-php-commands` -> `sites/laravel/php-commands.html`
- `laravel-frontend` -> `sites/laravel/frontend.html`
- `laravel-blade-ui` -> `sites/laravel/blade-ui.html`
- `laravel-data` -> `sites/laravel/data.html`
- `laravel-request-flow` -> `sites/laravel/request-flow.html`
- `laravel-container-architecture` -> `sites/laravel/container-architecture.html`
- `laravel-auth-security` -> `sites/laravel/auth-security.html`
- `laravel-api-integration` -> `sites/laravel/api-integration.html`
- `laravel-files-media` -> `sites/laravel/files-media.html`
- `laravel-async` -> `sites/laravel/async.html`
- `laravel-performance-search` -> `sites/laravel/performance-search.html`
- `laravel-realtime` -> `sites/laravel/realtime.html`
- `laravel-quality` -> `sites/laravel/quality.html`
- `laravel-devops` -> `sites/laravel/devops.html`
- `laravel-maintenance-upgrade` -> `sites/laravel/maintenance-upgrade.html`
- `laravel-repo-map` -> `sites/laravel/repo-map.html`

Interview pages:

- `interview` -> `sites/interview/index.html`
- `interview-fresher` -> `sites/interview/fresher.html`
- `interview-junior` -> `sites/interview/junior.html`
- `interview-intermediate` -> `sites/interview/intermediate.html`
- `interview-senior` -> `sites/interview/senior.html`
- `interview-master` -> `sites/interview/master.html`
- `interview-devops` -> `sites/interview/devops.html`

Vibe Coding pages:

- `vibe-coding` -> `sites/vibe-coding/index.html`
- `vibe-prompting` -> `sites/vibe-coding/prompting.html`
- `vibe-ai-crud` -> `sites/vibe-coding/ai-crud.html`
- `vibe-ai-review` -> `sites/vibe-coding/ai-review.html`
- `vibe-ai-runtime` -> `sites/vibe-coding/ai-runtime.html`

Legacy aliases in `LEGACY_PAGE_KEY_MAP`:

- `videos` -> `vibe-coding`
- `video-foundations` -> `vibe-prompting`
- `video-laravel-builds` -> `vibe-ai-crud`
- `video-debug-refactor` -> `vibe-ai-review`
- `video-devops-runtime` -> `vibe-ai-runtime`

## Main Content Files

Root data files:

- `data/site-content.en.json`
- `data/site-content.vi.json`
- `data/site-content.json`

The `.en.json` and `.vi.json` files are the active language-specific files loaded by `getContentPath(language)`.

PHP level files:

- `data/php/starter.en.json`
- `data/php/starter.vi.json`
- `data/php/intermediate.en.json`
- `data/php/intermediate.vi.json`
- `data/php/advanced.en.json`
- `data/php/advanced.vi.json`

Laravel topic files:

- `data/laravel/overview.en.json`
- `data/laravel/overview.vi.json`
- `data/laravel/structure.en.json`
- `data/laravel/structure.vi.json`
- `data/laravel/composer.en.json`
- `data/laravel/composer.vi.json`
- `data/laravel/php-commands.en.json`
- `data/laravel/php-commands.vi.json`
- `data/laravel/frontend.en.json`
- `data/laravel/frontend.vi.json`
- `data/laravel/blade-ui.en.json`
- `data/laravel/blade-ui.vi.json`
- `data/laravel/data.en.json`
- `data/laravel/data.vi.json`
- `data/laravel/request-flow.en.json`
- `data/laravel/request-flow.vi.json`
- `data/laravel/container-architecture.en.json`
- `data/laravel/container-architecture.vi.json`
- `data/laravel/auth-security.en.json`
- `data/laravel/auth-security.vi.json`
- `data/laravel/api-integration.en.json`
- `data/laravel/api-integration.vi.json`
- `data/laravel/files-media.en.json`
- `data/laravel/files-media.vi.json`
- `data/laravel/async.en.json`
- `data/laravel/async.vi.json`
- `data/laravel/performance-search.en.json`
- `data/laravel/performance-search.vi.json`
- `data/laravel/realtime.en.json`
- `data/laravel/realtime.vi.json`
- `data/laravel/quality.en.json`
- `data/laravel/quality.vi.json`
- `data/laravel/devops.en.json`
- `data/laravel/devops.vi.json`
- `data/laravel/maintenance-upgrade.en.json`
- `data/laravel/maintenance-upgrade.vi.json`
- `data/laravel/repo-map.en.json`
- `data/laravel/repo-map.vi.json`

Interview topic files:

- `data/interview/fresher.en.json`
- `data/interview/fresher.vi.json`
- `data/interview/junior.en.json`
- `data/interview/junior.vi.json`
- `data/interview/intermediate.en.json`
- `data/interview/intermediate.vi.json`
- `data/interview/senior.en.json`
- `data/interview/senior.vi.json`
- `data/interview/master.en.json`
- `data/interview/master.vi.json`
- `data/interview/devops.en.json`
- `data/interview/devops.vi.json`

Vibe Coding topic files:

- `data/vibe-coding/prompting.en.json`
- `data/vibe-coding/prompting.vi.json`
- `data/vibe-coding/ai-crud.en.json`
- `data/vibe-coding/ai-crud.vi.json`
- `data/vibe-coding/ai-review.en.json`
- `data/vibe-coding/ai-review.vi.json`
- `data/vibe-coding/ai-runtime.en.json`
- `data/vibe-coding/ai-runtime.vi.json`

## Menus

Menus for the Laravel, Interview, and Vibe Coding hubs live in `data/site-content.{language}.json`.

Laravel menu anchors:

- `laravel-overview`
- `laravel-structure`
- `laravel-composer`
- `laravel-php-commands`
- `laravel-frontend`
- `laravel-blade-ui`
- `laravel-data`
- `laravel-request-flow`
- `laravel-container-architecture`
- `laravel-auth-security`
- `laravel-api-integration`
- `laravel-files-media`
- `laravel-async`
- `laravel-performance-search`
- `laravel-realtime`
- `laravel-quality`
- `laravel-devops`
- `laravel-maintenance-upgrade`
- `laravel-repo-map`

Interview menu anchors:

- `interview-fresher`
- `interview-junior`
- `interview-intermediate`
- `interview-senior`
- `interview-master`
- `interview-devops`

Vibe Coding menu anchors:

- `vibe-prompting`
- `vibe-ai-crud`
- `vibe-ai-review`
- `vibe-ai-runtime`

## Section Types

`renderSection(section, language)` recognizes these section shapes:

- `type: "mindmap"` uses `createMindmapSection`.
- `type: "list"` uses `createGroupedBulletList` and progress tracking.
- `type: "links"` uses link cards.
- Any other type falls back to content cards.

List sections can use:

- `items`
- `breaks`
- `questionNumbered`
- `questionStyle`
- `anchor`
- `heading`
- `intro`

Item fields commonly used:

- `title`
- `body`
- `bullets`
- `code`
- `tip`
- `note`
- `links`

## Search Index

Global search is built in `buildSearchIndex(data, language)`.

It indexes:

- pages in `data/site-content.{language}.json`
- menu items
- page sections
- Laravel topic JSON files
- Interview topic JSON files
- Vibe Coding topic JSON files
- PHP level JSON files

If adding a new topic family, update search indexing too.

## Progress Features

Progress is stored in localStorage. Avoid changing IDs casually.

Progress ID format:

```text
{pageKey}::{sectionKey}::{itemIndex}::{slugified-title}
```

Changing headings, section anchors, or item order can affect saved progress.

## Laravel Labs Map

For detailed Laravel lab inventory, read `docs/laravel-labs-inventory.md`.

`hub/`:

- Aggregate Laravel app for the portal content.
- Reads `data/**` through `config/labs.php` and a JSON repository.
- Important routes: `/`, `/questions`, `/sources/{sourceKey}`.
- Integration notes live at `hub/INTEGRATION_NOTES.md`.

`breeze/`:

- Laravel Breeze-style auth scaffold.
- Important routes: `routes/web.php`, `routes/auth.php`.
- Uses profile controller and Blade auth views.
- Frontend tooling: Vite, Tailwind, Alpine.

`chirper/`:

- Jetstream-style scaffold with Livewire and Sanctum.
- Contains `App\Models\Chirp`.
- Contains `database/migrations/2024_09_17_021924_create_chirps_table.php`.
- Current chirp feature appears incomplete.

`jetstream/`:

- Jetstream-style scaffold with teams.
- Important model files include `User`, `Team`, `Membership`, and `TeamInvitation`.
- Includes team policies and Jetstream action classes.

`sail/`:

- Laravel Sail-oriented app.
- `docker-compose.yml` defines `laravel.test`, MySQL, Redis, Meilisearch, Mailpit, and Selenium.
- Exposes app port using `${APP_PORT:-87}:87` in the observed compose file.

Each Laravel folder should be treated as an independent project with its own `composer.json`, `package.json`, tests, and environment.
