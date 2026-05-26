# AI Review Checklist

Use this checklist when reviewing or changing this repository.

## Static Site Checks

- Did the AI read `docs/static-site-architecture.md` before changing runtime behavior?
- Did the AI read `docs/data-schema.md` before changing JSON content shape?
- Did the AI read `docs/content-taxonomy.md` before adding or reorganizing learning content?
- Did the AI read `docs/content-quality-standards.md` before writing, rewriting, or translating content?
- Did the AI read `docs/design-system.md` before changing CSS or UI markup?
- Did the AI check `docs/technical-debt.md` before fixing a known issue?
- Does the page wrapper have the correct `data-page`?
- Does the page wrapper have the correct `data-site-root`?
- Does `assets/shell.js` resolve the expected partial shell?
- Does the partial shell contain all IDs used by the renderer?
- Are links built through `getPageHref()` or `SITE_ROOT` instead of hardcoded relative paths?
- Are user-facing strings escaped before being inserted with `innerHTML`?
- Are bilingual fields handled through `text(value, language)`?
- Does language switching re-render the updated content cleanly?
- Does theme switching preserve the current page and language?
- Does progress/localStorage logic tolerate malformed stored data?
- Do JSON files parse successfully?

## Content Checks

- Does the content follow the tone and quality rules in `docs/content-quality-standards.md`?
- Keep English and Vietnamese versions aligned when changing shared content.
- Do not delete Vietnamese text because terminal output looks broken.
- Preserve code blocks exactly unless the task is to correct them.
- Check that new anchors are unique and stable.
- Avoid changing progress IDs accidentally by renaming headings without reason.
- Avoid vague advice. Add concrete examples, failure modes, tradeoffs, or checks where useful.
- Keep interview answers practical and role-appropriate.
- Keep glossary definitions short but useful.
- Keep practice tasks actionable and verifiable.

## CSS/UI Checks

- Does the change preserve the warm editorial learning-portal style described in `docs/design-system.md`?
- Check desktop and mobile layout if markup or CSS changes.
- Avoid nested card layouts unless already established locally.
- Keep controls predictable and accessible.
- Make sure long Vietnamese text does not overflow buttons, cards, or nav items.
- Keep visual changes scoped to the affected component.

## JavaScript Checks

- Avoid introducing global variables unless they match the existing style.
- If adding a new renderer, keep it close to related render helpers.
- Prefer small helper functions when the same escaping/path logic repeats.
- Avoid relying on Node-only tooling for runtime behavior because this is a browser static site.
- Handle fetch failures with clear fallback messaging.

## Laravel Lab Checks

- Did the AI read `docs/laravel-labs-inventory.md` before editing a Laravel lab?
- Did the AI read `docs/principal-laravel-architect-rules.md` before generating, refactoring, reviewing, or architecting Laravel code?
- Did the AI read `docs/laravel-coding-standards.md` before writing or reviewing Laravel app code?
- Did the AI read `docs/engineering-standards.md` before changing database, API, security, tests, logs, dependencies, env, or Git workflow?
- Treat each child folder as an independent Laravel app.
- Check the app's own `composer.json`, routes, migrations, models, and tests.
- Do not assume all Laravel folders use the same version or scaffold.
- Keep controllers thin: validation, authorization, orchestration, response.
- Put business logic in service/action classes.
- Put reusable query/database access logic in repositories or query classes when the feature grows beyond simple Eloquent calls.
- Keep model methods focused on relationships, casts, scopes, and small domain helpers.
- Do not put raw SQL in controllers.
- Wrap multi-step writes in transactions.
- Add or update tests for changed behavior.
- Keep migrations reversible and avoid destructive schema changes without an explicit plan.
- Do not expose secrets in code, docs, examples, logs, or tests.
- Do not add dependencies unless the codebase genuinely needs them.
- If PHP/composer are available, run tests for the specific app touched.
- If frontend assets are touched inside a Laravel app, check its `package.json` before running npm commands.

## Known Risk Areas

- Hardcoded relative links in partials.
- Raw JSON content rendered into `innerHTML`.
- Large `assets/site.js` file with many responsibilities.
- Static page wrappers can drift from `PAGE_PATHS`.
- Laravel lab folders may contain scaffold/demo code that is intentionally incomplete.

## Suggested Verification Commands

Use what is available on the current machine.

```powershell
git status --short
Get-Content AGENTS.md
Get-Content docs\local-setup-and-verification.md
rg -n "href=\"../index.html\"" partials
Get-ChildItem -Recurse -Filter *.json | ForEach-Object {
  $file = $_.FullName
  try {
    Get-Content -Raw -LiteralPath $file | ConvertFrom-Json | Out-Null
  } catch {
    Write-Output ("JSON FAIL: " + $file + " :: " + $_.Exception.Message)
  }
}
```

If Node/PHP/Composer are installed:

```powershell
node -v
php -v
composer --version
```

Then run project-specific test commands only after checking the relevant package files.
