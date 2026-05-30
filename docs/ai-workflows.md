# AI Workflows

This file gives repeatable workflows for AI assistants maintaining Laravel Labs.

## Start Of Every Session

Run read-only discovery first:

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

Then inspect the specific files for the current request.

## Review The Static Site

1. Check `git status --short`.
2. Read `assets/shell.js`.
3. Read the relevant page wrapper in `sites/**`.
4. Read the relevant partial in `partials/**`.
5. Read the relevant render path in `assets/site.js`.
6. Read the relevant JSON files in `data/**`.
7. Read `docs/static-site-architecture.md` for related behavior.
8. Read `docs/data-schema.md` if JSON shape is involved.
9. Read `docs/design-system.md` if UI or CSS is involved.
10. Read `docs/content-taxonomy.md` if page purpose or learning structure is involved.
11. Read `docs/content-quality-standards.md` if writing or rewriting content.
12. Check that links use `SITE_ROOT` or `getPageHref()`.
13. Check that JSON-backed text is escaped before `innerHTML`.
14. Parse JSON files.
15. If browser tooling is available, run the site through a local HTTP server and smoke test the touched pages.

## Add A New Laravel Topic

Use this when adding a new page under `sites/laravel`.

1. Add the HTML wrapper under `sites/laravel/{topic}.html`.
2. Set `data-page="laravel-{topic}"`.
3. Set `data-site-root="../.."`.
4. Add `laravel-{topic}` to `PAGE_PATHS` in `assets/site.js`.
5. Add the menu item to `data/site-content.en.json`.
6. Add the matching menu item to `data/site-content.vi.json`.
7. Add `data/laravel/{topic}.en.json`.
8. Add `data/laravel/{topic}.vi.json`.
9. Confirm `loadLaravelSection(language, pageKey)` maps to the correct file name.
10. Check global search includes the new topic through the Laravel menu.

Do not add a Laravel topic file without adding its menu item, because search and topic navigation depend on the menu.

## Add A New Interview Topic

1. Add `sites/interview/{topic}.html`.
2. Set `data-page="interview-{topic}"`.
3. Add `interview-{topic}` to `PAGE_PATHS`.
4. Add matching menu entries to `data/site-content.en.json` and `data/site-content.vi.json`.
5. Add `data/interview/{topic}.en.json`.
6. Add `data/interview/{topic}.vi.json`.
7. Confirm `loadInterviewSection(language, pageKey)` resolves the file.
8. Check topic switcher and global search.

## Add A New Vibe Coding Topic

1. Add `sites/vibe-coding/{topic}.html`.
2. Choose the page key pattern `vibe-{topic}`.
3. Add the page key to `PAGE_PATHS`.
4. Add matching menu entries to `data/site-content.en.json` and `data/site-content.vi.json`.
5. Add `data/vibe-coding/{topic}.en.json`.
6. Add `data/vibe-coding/{topic}.vi.json`.
7. Confirm `getVideoTopicKey(pageKey)` maps the page key to the file name.
8. Add a legacy alias only if an old URL/page key already exists.

## Add A New PHP Level

PHP levels are more coupled than topic pages.

1. Add `sites/php/{level}.html`.
2. Add a `php-{level}` entry to `PAGE_PATHS`.
3. Add `{ key, path }` to `PHP_LEVELS`.
4. Add the level to `PHP_LEVEL_PAGE_MAP`.
5. Add header menu labels in `PHP_HEADER_MENU.en` and `PHP_HEADER_MENU.vi`.
6. Add `data/php/{level}.en.json`.
7. Add `data/php/{level}.vi.json`.
8. Update PHP overview content in `data/site-content.en.json` and `data/site-content.vi.json` if needed.
9. Test level switcher, resume prompt, keyword directory, code toggles, and search.

## Update Existing Content

1. Identify whether the content lives in `data/site-content.*.json` or a topic JSON file.
2. Read `docs/content-taxonomy.md` to keep the content in the right learning area.
3. Read `docs/content-quality-standards.md` to keep the writing professional and consistent.
4. Choose the existing renderer shape before writing content:
   - default card section for independent related concepts
   - `type: "list"` for grouped notes, checklists, Q&A, or progress-trackable content
   - `type: "links"` for references
   - PHP `modules` or `phases` for PHP level blocks
5. Keep related title, description, bullets, code, notes, and links inside that shape instead of adding loose paragraphs or raw HTML.
6. Update English and Vietnamese files together when the concept exists in both languages.
7. Preserve anchors unless intentionally changing URLs or progress IDs.
8. Keep code snippets escaped as JSON strings.
9. Parse JSON after editing.
10. Check the rendered page if browser tooling is available, including one mobile width when code blocks, long titles, or Vietnamese copy are touched.

## Fix Or Add Render Helpers

1. Find all call sites that render the same content shape.
2. Prefer `escapeHtml()` for plain text.
3. Prefer `formatRichText()` only when inline backtick formatting is desired.
4. Escape attribute values too, not just text nodes.
5. Be careful with `href`; validate whether links are internal or trusted external references.
6. Keep behavior stable for existing JSON content.

## Work On A Laravel Lab

1. Enter only the specific folder requested by the user.
2. Read `docs/laravel-labs-inventory.md`.
3. Read `docs/principal-laravel-architect-rules.md`.
4. Read `docs/laravel-coding-standards.md`.
5. Read `docs/engineering-standards.md`.
6. Read that folder's `composer.json`.
7. Read that folder's `package.json` if frontend assets are involved.
8. Read routes, models, migrations, controllers/actions, views, and tests for the requested feature.
9. Do not assume another lab folder has the same structure.
10. If tools are available, run the app-specific tests from that folder.
11. Do not modify `.env`; use `.env.example` for documented defaults.

## Document New Findings

If a finding should survive into future AI sessions:

1. Add a short rule to `AGENTS.md` if every AI must know it immediately.
2. Add structural knowledge to `docs/ai-context.md` or `docs/content-map.md`.
3. Add process knowledge to `docs/ai-workflows.md`.
4. Add setup or command knowledge to `docs/local-setup-and-verification.md`.
5. Add design knowledge to `docs/design-system.md`.
6. Add learning taxonomy knowledge to `docs/content-taxonomy.md`.
7. Add review checks to `docs/ai-review-checklist.md`.
8. Add known issues to `docs/technical-debt.md`.
9. Add decisions and session notes to `docs/ai-change-log.md`.

Keep docs factual and concise. Avoid turning these files into a full tutorial.
