# AI Context

This document summarizes the repository so a new AI assistant can become useful quickly without rediscovering the same structure every session.

## What This Repo Is

Laravel Labs is a personal learning portal for:

- PHP foundations
- Laravel concepts
- Interview preparation by level
- AI-assisted coding workflows
- Glossary and practice material

It is not one single Laravel app. It is a static learning site, one aggregate Laravel hub app, and several standalone Laravel lab folders.

## Top-Level Structure

- `index.html`  
  Static site entry page.

- `assets/`  
  Shared CSS and JavaScript for the static portal.

- `assets/site.js`  
  Main renderer and behavior layer. It loads JSON content, renders pages, handles language switching, theme switching, progress tracking, search, breadcrumbs, topic switchers, and PHP keyword navigation.

- `assets/shell.js`  
  Loads shared HTML partials based on `document.body.dataset.page`.

- `assets/styles.css` and `assets/css/*.css`  
  Shared styling split into ordered CSS modules.

- `partials/`  
  Shared HTML shells. These are fetched at runtime by `assets/shell.js`.

- `sites/`  
  Thin HTML page wrappers. Each page mostly sets `data-page` and `data-site-root`, then loads shared assets.

- `data/`  
  User-facing JSON content. Most pages are rendered from this data. It contains English and Vietnamese variants.

- `hub/`  
  Aggregate Laravel application that reads the static portal JSON files from `data/**` and exposes a Blade dashboard, source browser, and searchable question bank.

- `breeze/`, `chirper/`, `jetstream/`, `sail/`  
  Independent Laravel projects used as labs/examples. They should be treated as separate apps.

## Static Site Runtime Model

Each static page follows this flow:

1. HTML wrapper sets `data-page` and `data-site-root`.
2. `assets/shell.js` chooses a partial shell and injects it.
3. `assets/site.js` loads `data/site-content.{language}.json`.
4. Depending on `data-page`, `site.js` loads additional JSON content.
5. Renderer functions write into placeholders from the partial shell.

Important page families:

- `landing`
- `roadmap`
- `glossary`
- `practice`
- `php`, `php-*`
- `laravel`, `laravel-*`
- `interview`, `interview-*`
- `vibe-coding`, `vibe-*`

## Content And Language

The site supports English and Vietnamese:

- Default language: `en`
- Supported languages: `en`, `vi`
- Language preference key: `laravel-labs-language`

Most content fields can be either:

- a string
- an object like `{ "en": "...", "vi": "..." }`

Use the existing `text(value, language)` helper when reading multilingual values.

## Rendering Safety

The site uses `innerHTML` heavily. Some helpers already use:

- `escapeHtml(value)`
- `formatRichText(value)`

When adding new render paths, do not inject raw JSON text unless the field is intentionally trusted HTML. Prefer escaped text, and only allow small formatting features through a controlled helper.

## Navigation Model

Page URLs are centralized in `PAGE_PATHS` inside `assets/site.js`.

Use `getPageHref(pageKey)` instead of hardcoding paths in JavaScript.

HTML partials should not assume a fixed nesting depth. Use `SITE_ROOT` where possible.

## Local Storage Keys

The static site stores user preferences and progress:

- `laravel-labs-language`
- `laravel-labs-theme`
- `laravel-labs-php-last-level`
- `laravel-labs-laravel-last-topic`
- `laravel-labs-interview-last-topic`
- `laravel-labs-vibe-coding-last-topic`
- `laravel-labs-roadmap-progress`
- `laravel-labs-roadmap-collapsed`
- `laravel-labs-content-progress`

Do not rename these keys without a migration/fallback plan.

## Laravel Lab Notes

The Laravel folders are independent projects. Check the specific folder before assuming a framework version or structure.

Observed examples:

- `breeze` is Laravel Breeze-style auth scaffolding.
- `jetstream` is Jetstream-style scaffolding with teams.
- `chirper` is Jetstream-style and contains a partial `Chirp` model/migration, but the actual chirp feature appears incomplete.
- `sail` is a Laravel project with Sail/docker-compose context.
- `hub` is a Laravel 13 aggregate app. It is read-only against `../data/**` and does not currently require a database for its core JSON views.

Do not run destructive commands or reset generated files in these projects unless explicitly requested.

## Recommended First Checks For A New AI Session

Run these read-only checks first:

```powershell
git status --short
rg --files
Get-Content AGENTS.md
Get-Content docs\README.md
Get-Content docs\ai-context.md
Get-Content docs\rule-precedence.md
Get-Content docs\definition-of-done.md
Get-Content docs\content-map.md
Get-Content docs\static-site-architecture.md
Get-Content docs\data-schema.md
Get-Content docs\content-taxonomy.md
Get-Content docs\content-quality-standards.md
Get-Content docs\design-system.md
Get-Content docs\local-setup-and-verification.md
Get-Content docs\laravel-labs-inventory.md
Get-Content docs\principal-laravel-architect-rules.md
Get-Content docs\laravel-coding-standards.md
Get-Content docs\engineering-standards.md
Get-Content docs\templates.md
Get-Content docs\adr-guide.md
Get-Content docs\ai-workflows.md
Get-Content docs\ai-review-checklist.md
Get-Content docs\technical-debt.md
Get-Content docs\ai-change-log.md
```

Then inspect only the files related to the user request.

## Related Documentation

- `AGENTS.md`: first-read rules for AI agents.
- `docs/README.md`: documentation index and task-based reading guide.
- `docs/rule-precedence.md`: conflict resolution between rules.
- `docs/definition-of-done.md`: quality gates by task type.
- `docs/content-map.md`: current page keys, topic files, shells, and data map.
- `docs/static-site-architecture.md`: runtime behavior and renderer responsibilities.
- `docs/data-schema.md`: JSON data shapes and field meanings.
- `docs/content-taxonomy.md`: learning areas, page purpose, and topic organization.
- `docs/content-quality-standards.md`: professional writing, bilingual quality, examples, interview answers, glossary, practice content, and publish criteria.
- `docs/design-system.md`: CSS modules, UI components, style rules, and responsive behavior.
- `docs/local-setup-and-verification.md`: setup and verification commands.
- `docs/laravel-labs-inventory.md`: Laravel lab versions, dependencies, routes, migrations, tests, and focus.
- `docs/principal-laravel-architect-rules.md`: senior/principal Laravel architecture output rules and enterprise-grade code expectations.
- `docs/laravel-coding-standards.md`: Laravel coding structure, layering, comments, docblocks, SQL/repository/service/controller rules, and tests.
- `docs/engineering-standards.md`: database, API, security, testing, logging, dependency, environment, performance, and Git rules.
- `docs/templates.md`: reusable plans, review, API, technical documentation, and content templates.
- `docs/adr-guide.md`: architecture decision record guidance.
- `docs/ai-workflows.md`: step-by-step workflows for common maintenance tasks.
- `docs/ai-review-checklist.md`: review checklist.
- `docs/technical-debt.md`: known issues, risk, and suggested fix order.
- `docs/ai-change-log.md`: durable findings and follow-up notes.
