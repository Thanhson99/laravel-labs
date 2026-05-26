# AI Working Rules For Laravel Labs

This file is the first file an AI assistant should read when working in this repository.

## Read Order

1. Read this file first.
2. Read `docs/README.md` to choose task-specific docs.
3. Read `docs/ai-context.md` to understand the repository structure and product goal.
4. Read `docs/rule-precedence.md` to resolve conflicting rules.
5. Read `docs/definition-of-done.md` to know the quality gate for the task.
6. Read `docs/content-map.md` when the task touches pages, topics, JSON content, links, or navigation.
7. Read `docs/static-site-architecture.md` when the task touches rendering, navigation, search, progress, shell loading, or frontend behavior.
8. Read `docs/data-schema.md` when the task touches JSON content.
9. Read `docs/content-taxonomy.md` when the task touches learning content, page purpose, or topic organization.
10. Read `docs/content-quality-standards.md` before writing, rewriting, translating, or reorganizing learning content.
11. Read `docs/design-system.md` when the task touches CSS, layout, UI components, or visual consistency.
12. Read `docs/local-setup-and-verification.md` before running or suggesting commands.
13. Read `docs/laravel-labs-inventory.md` when the task touches `hub`, `breeze`, `chirper`, `jetstream`, or `sail`.
14. Read `docs/principal-laravel-architect-rules.md` before generating, refactoring, reviewing, or architecting Laravel code.
15. Read `docs/laravel-coding-standards.md` before writing or reviewing Laravel application code.
16. Read `docs/engineering-standards.md` before changing database, API, security, tests, logging, dependencies, environment, or Git workflow.
17. Read `docs/templates.md` when producing plans, reviews, API docs, technical docs, or structured content.
18. Read `docs/adr-guide.md` when making durable architecture or process decisions.
19. Read `docs/ai-workflows.md` for repeatable steps to add pages, topics, docs, or review changes.
20. Read `docs/ai-review-checklist.md` before reviewing or changing code.
21. Read `docs/technical-debt.md` before fixing or triaging known issues.
22. Read `docs/ai-change-log.md` to see known findings and decisions from previous AI sessions.
23. Read the specific files related to the current task before editing.

## Repository Goal

Laravel Labs is a bilingual learning portal for PHP, Laravel, interview preparation, and AI-assisted coding. The root site is a static GitHub Pages-style portal. The folder `hub` is the aggregate Laravel application for integrating portal JSON content. The folders `breeze`, `chirper`, `jetstream`, and `sail` are independent Laravel example projects used as learning labs.

The main product experience is the static site:

- `index.html`
- `sites/**`
- `partials/**`
- `assets/**`
- `data/**`

The Laravel folders are supporting examples unless the user asks specifically to work on one of them.

## Working Rules

- Keep changes scoped to the requested task.
- Do not rewrite large content files unless the task is specifically about content.
- Preserve bilingual English/Vietnamese behavior.
- Prefer existing patterns in `assets/site.js`, `assets/shell.js`, `partials`, and `data`.
- Treat JSON files under `data/` as user-facing content.
- Escape user-facing HTML output when rendering JSON content into `innerHTML`.
- Do not remove existing Vietnamese content because it appears garbled in a terminal. PowerShell may display UTF-8 text incorrectly, while the file itself can still be valid.
- Do not modify generated dependency folders such as `vendor`, `node_modules`, or build output.
- Before finalizing frontend changes, check internal links and responsive behavior where possible.
- In Laravel apps with many routes, keep `routes/web.php` and `routes/api.php` as entry points and split route definitions into grouped files under `routes/web/**` and `routes/api/**`.
- In Laravel learning/practice apps, every route file should have a short English file-level comment and every route should have a concise purpose comment so learners can scan the URL map.
- Keep the `hub` Laravel UI copy in English. Do not mix Vietnamese without accents into Blade, config-driven UI copy, or practice service payloads.
- Keep folder responsibilities strict: routes map URLs, controllers orchestrate, requests validate, services/actions handle workflows, repositories/queries handle persistence, views render prepared data, and tests verify behavior.

## Important Known Issues

- The shared partials currently hardcode `href="../index.html"` for `#backLink`. On nested pages this can point to `sites/index.html`, which does not exist. Prefer setting this link through JavaScript using `SITE_ROOT`.
- `assets/site.js` renders many JSON fields through template strings and `innerHTML`. Some values are escaped, but not all. When touching render helpers, standardize safe escaping.
- The `chirper` app contains a `Chirp` model and migration, but the feature appears incomplete: the migration has only `id` and timestamps, and routes/views do not expose chirp CRUD yet.

## Environment Notes

The current observed machine did not have these commands available:

- `php`
- `composer`
- `node`

If a future machine has them installed, run normal verification for the touched area:

- Static site: local HTTP server plus browser smoke test.
- Laravel apps: `composer install`, `php artisan test`, and relevant npm commands if frontend assets are touched.

## Updating These Docs

When an AI session discovers a durable rule, repo pattern, or unresolved issue, update one of these files:

- `AGENTS.md` for short rules that every future AI must read.
- `docs/ai-context.md` for repository structure and mental model.
- `docs/README.md` for documentation index and task-based reading guide.
- `docs/rule-precedence.md` for conflict resolution between rules.
- `docs/definition-of-done.md` for task completion quality gates.
- `docs/content-map.md` for page keys, data files, route maps, and content structure.
- `docs/static-site-architecture.md` for frontend runtime behavior and renderer responsibilities.
- `docs/data-schema.md` for JSON shapes and field meanings.
- `docs/content-taxonomy.md` for learning areas, content purpose, and topic organization.
- `docs/content-quality-standards.md` for professional writing, bilingual quality, examples, interview answers, glossary, practice content, and publish criteria.
- `docs/design-system.md` for UI style, CSS modules, components, and responsive rules.
- `docs/local-setup-and-verification.md` for machine setup and command guidance.
- `docs/laravel-labs-inventory.md` for per-lab Laravel versions, packages, routes, tests, and migrations.
- `docs/principal-laravel-architect-rules.md` for senior/principal Laravel architecture output rules and enterprise-grade code expectations.
- `docs/laravel-coding-standards.md` for Laravel folder structure, layering, comments, docblocks, SQL/repository/service/controller rules, validation, transactions, and tests.
- `docs/engineering-standards.md` for database, API, security, testing, logging, dependency, environment, performance, and Git rules.
- `docs/templates.md` for reusable plans, API docs, review output, technical docs, content topics, practice tasks, and commit messages.
- `docs/adr-guide.md` for architecture decision records.
- `docs/ai-workflows.md` for repeatable AI workflows and editing playbooks.
- `docs/ai-review-checklist.md` for repeatable review checks.
- `docs/technical-debt.md` for known issues and recommended fix order.
- `docs/ai-change-log.md` for findings, decisions, and follow-up tasks.
