# Technical Debt And Known Issues

This file tracks known issues, risks, and recommended fix order. Keep it practical and update it when issues are fixed or new ones are found.

## Priority 1: Broken Back Link In Shared Partials

Files:

- `partials/detail-shell.html`
- `partials/php-level-shell.html`
- `partials/php-overview-shell.html`
- `partials/topic-shell.html`

Issue:

```html
<a class="back-link" href="../index.html" id="backLink"></a>
```

For pages under `sites/{section}/index.html` or `sites/{section}/{topic}.html`, this can resolve to `sites/index.html`, which does not exist.

Recommended fix:

- Set the `href` after shell load using `SITE_ROOT`.
- Keep the label from `data.common.backToHome`.
- Do not rely on fixed relative depth inside partials.

Example direction:

```javascript
const backLink = document.getElementById("backLink");
if (backLink) {
  backLink.href = `${SITE_ROOT}/index.html`;
}
```

Where to place:

- Near common page rendering setup in `assets/site.js`, so all non-landing pages get the same behavior.

## Priority 1: Raw JSON Content Rendered Into innerHTML

File:

- `assets/site.js`

Issue:

The site uses many template strings and `innerHTML`. Some render paths use `escapeHtml()` or `formatRichText()`, but many JSON-backed fields are inserted raw.

Known raw or partially raw areas include:

- landing cards
- repo cards
- content cards
- link cards
- roadmap labels and references
- PHP module titles
- PHP question titles
- phase headings
- header nav labels and descriptions

Risk:

- Broken layout if content contains accidental markup.
- XSS if untrusted content is ever added to JSON.

Recommended fix:

- Add clear helper names for escaped text and escaped rich text.
- Escape attribute values.
- Convert render helpers gradually, one component group at a time.
- After each group, smoke test English and Vietnamese pages.

Do not blindly escape code snippets twice. Code snippets should remain readable in `<pre><code>` and should use `escapeHtml`.

## Priority 2: `chirper` Feature Is Incomplete

Files:

- `chirper/app/Models/Chirp.php`
- `chirper/database/migrations/2024_09_17_021924_create_chirps_table.php`
- `chirper/routes/web.php`
- `chirper/resources/views/dashboard.blade.php`

Issue:

There is a `Chirp` model and a `chirps` table migration, but the table has only `id` and timestamps. There is no visible CRUD route or UI for chirps.

Recommended decision:

- If `chirper` is meant to be a complete lab, implement the feature.
- If it is intentionally a partial scaffold, document that in the portal content.

Likely implementation if completing:

- Add `user_id`.
- Add `message` or `body`.
- Add `fillable` fields.
- Add `User` relationship.
- Add routes/controller or Livewire component.
- Add create/list/edit/delete UI.
- Add authorization so users can only update/delete their own chirps.
- Add feature tests.

## Priority 2: `assets/site.js` Is Large And Multi-Concern

File:

- `assets/site.js`

Issue:

One file owns loading, rendering, state, search, navigation, progress, and specialized page behavior.

Risk:

- Changes in one area can accidentally affect unrelated pages.
- Harder for future AI or humans to reason about render safety.

Recommended approach:

- Do not refactor this all at once.
- If making a feature change, keep edits localized.
- If splitting later, split by stable domain:
  - core utilities
  - data loading
  - navigation/breadcrumbs
  - search
  - progress
  - roadmap
  - PHP levels
  - topic pages

Only split when a test/smoke-test path is available.

## Priority 2: Page Wrapper Drift Risk

Files:

- `sites/**/*.html`
- `assets/site.js`

Issue:

Each page wrapper must keep `data-page`, `data-site-root`, scripts, and stylesheet paths aligned with `PAGE_PATHS`.

Risk:

- A new page can exist but fail to render because `data-page` does not match menu and path config.

Recommended mitigation:

- Add a script later to validate wrappers against `PAGE_PATHS`.
- At minimum, use the workflow in `docs/ai-workflows.md` when adding pages.

## Priority 3: Search Index Has No Validation For Missing Topic Files

File:

- `assets/site.js`

Issue:

Global search loads topic files based on menu anchors. If a menu item is added without a matching JSON file, search can fail.

Recommended mitigation:

- Add a validation script that checks every menu anchor has matching `.en.json`, `.vi.json`, and wrapper HTML.
- Keep `docs/content-map.md` updated.

## Priority 3: No Automated Static Site Test

Issue:

There is no automated smoke test for:

- shell fetch success
- JSON fetch success
- global nav links
- topic render paths
- language switching
- search
- progress buttons

Recommended mitigation:

- Add a small Playwright suite if Node is added to the project.
- Keep it focused on representative pages instead of every long content page.

## Priority 3: PowerShell Encoding Display Can Mislead Review

Issue:

Vietnamese text may display as mojibake in PowerShell output even when the source file is valid UTF-8.

Recommended rule:

- Do not "fix" Vietnamese text based only on terminal display.
- Verify in a UTF-8-aware editor or browser.
- Parse JSON and inspect rendered page when possible.
