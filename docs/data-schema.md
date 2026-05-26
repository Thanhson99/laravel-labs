# Data Schema

This document describes the JSON content shapes used by the static portal. It is not a formal JSON Schema file, but it should be treated as the working contract for AI edits.

## Shared Value Types

Many fields can be either a string or a localized object.

```json
"title": "English title"
```

or:

```json
"title": {
  "en": "English title",
  "vi": "Vietnamese title"
}
```

Use `text(value, language)` in JavaScript to read these values.

## Root Content File

Files:

- `data/site-content.en.json`
- `data/site-content.vi.json`

Top-level shape:

```json
{
  "defaultLanguage": "en",
  "languages": ["en", "vi"],
  "common": {},
  "pages": {}
}
```

`common` includes:

```json
{
  "backToHome": "Back to Portal",
  "navigation": {
    "home": "Home",
    "roadmap": "Roadmap",
    "glossary": "Glossary",
    "php": "PHP",
    "laravel": "Laravel",
    "practice": "Practice",
    "interview": "Interview",
    "vibeCoding": "Vibe Coding"
  },
  "theme": {
    "light": "Light",
    "dark": "Dark"
  }
}
```

`pages` maps page keys to page definitions.

## Basic Page Shape

Most pages use:

```json
{
  "metaTitle": "Page title for document.title",
  "eyebrow": "Short label",
  "title": "Visible page title",
  "subtitle": "Visible intro text",
  "sections": []
}
```

`renderDetail` uses:

- `eyebrow`
- `title`
- `subtitle`
- `sections`

## Landing Page Shape

The `landing` page uses:

```json
{
  "metaTitle": "",
  "eyebrow": "",
  "title": "",
  "subtitle": "",
  "sectionTitle": "",
  "cards": [],
  "repoSectionTitle": "",
  "repoSectionSubtitle": "",
  "repoCards": []
}
```

Landing card shape:

```json
{
  "href": "sites/php/index.html",
  "title": "PHP Fundamentals",
  "summary": "Short description",
  "button": "Open PHP Site"
}
```

Repo card shape:

```json
{
  "name": "breeze",
  "summary": "",
  "focusLabel": "Focus:",
  "focus": "",
  "nextLabel": "Next expansions:",
  "next": "",
  "openLabel": "Related labs:",
  "open": ""
}
```

## Hub Page Shape

Laravel, Interview, and Vibe Coding hubs use menus and topic navigation.

```json
{
  "metaTitle": "",
  "eyebrow": "",
  "title": "",
  "subtitle": "",
  "journeyTitle": "",
  "journeySubtitle": "",
  "menu": [],
  "sections": []
}
```

Menu item shape:

```json
{
  "anchor": "laravel-auth-security",
  "label": "Auth & security",
  "desc": "Short menu description"
}
```

The `anchor` must match a `PAGE_PATHS` key and the wrapper page `data-page`.

## Section Shapes

### Default Card Section

If `type` is omitted or not recognized, `renderSection` renders content cards.

```json
{
  "heading": "Section title",
  "intro": "Section intro",
  "items": [
    {
      "title": "Card title",
      "body": "Card body"
    }
  ]
}
```

### Links Section

```json
{
  "type": "links",
  "heading": "References",
  "intro": "Useful links",
  "items": [
    {
      "label": "Laravel Docs",
      "description": "Official documentation",
      "href": "https://laravel.com/docs",
      "action": "Open"
    }
  ]
}
```

### List Section

```json
{
  "type": "list",
  "anchor": "stable-section-key",
  "heading": "Checklist",
  "intro": "Intro text",
  "questionNumbered": false,
  "questionStyle": "",
  "breaks": [],
  "items": []
}
```

List item shape:

```json
{
  "title": "Item title",
  "body": "Item body",
  "bullets": ["Point one", "Point two"],
  "code": "<?php\n",
  "tip": "Tip text",
  "note": "Note text",
  "links": [
    {
      "href": "sites/php/index.html",
      "label": "PHP",
      "desc": "Related page"
    }
  ]
}
```

Break shape:

```json
{
  "start": 1,
  "title": "Part title",
  "description": "Optional description",
  "tone": "amber"
}
```

`start` is 1-based and points to the first item in the group.

### Mindmap Section

Used by the roadmap page.

```json
{
  "type": "mindmap",
  "heading": "",
  "intro": "",
  "legend": [],
  "root": {},
  "branches": [],
  "cta": {}
}
```

Legend item:

```json
{
  "tone": "core",
  "label": "Learn first"
}
```

Root shape:

```json
{
  "eyebrow": "",
  "title": "",
  "summary": ""
}
```

Branch shape:

```json
{
  "step": "01",
  "side": "left",
  "tone": "core",
  "badge": "Frontend",
  "title": "Frontend fundamentals",
  "summary": "",
  "topics": ["HTML", "CSS"],
  "subbranches": [],
  "references": [],
  "note": ""
}
```

Subbranch shape:

```json
{
  "title": "Main branch",
  "items": ["Semantic HTML", "Forms"]
}
```

Reference shape:

```json
{
  "href": "https://developer.mozilla.org/",
  "label": "MDN"
}
```

CTA shape:

```json
{
  "eyebrow": "",
  "title": "",
  "body": "",
  "href": "sites/php/index.html",
  "action": "Continue"
}
```

## Topic JSON Shape

Laravel, Interview, and Vibe Coding topic files generally use:

```json
{
  "heading": "Topic heading",
  "intro": "Topic intro",
  "type": "list",
  "items": []
}
```

The topic renderer wraps this as a section. For Laravel and Vibe topics, `renderSection` handles the normalized section. For Interview topics, the renderer explicitly builds an interview topic section and uses `createGroupedBulletList`.

## PHP Level Shape

PHP level files use a richer structure:

```json
{
  "anchor": "starter",
  "badge": "Level 1",
  "title": "",
  "actionLabel": "",
  "summary": "",
  "highlights": [],
  "modules": [],
  "phases": [],
  "examplesTitle": "",
  "examples": [],
  "questionsTitle": "",
  "questions": []
}
```

Module shape:

```json
{
  "title": "",
  "description": "",
  "bullets": []
}
```

Phase shape:

```json
{
  "badge": "",
  "title": "",
  "intro": "",
  "topics": [],
  "examples": []
}
```

Phase topic shape:

```json
{
  "term": "",
  "body": "",
  "note": ""
}
```

Example/snippet shape:

```json
{
  "title": "",
  "description": "",
  "code": "<?php\n"
}
```

Question shape:

```json
{
  "tag": "",
  "question": "",
  "answer": ""
}
```

## Inline Formatting

Backtick text is converted to:

```html
<span class="inline-keyword">...</span>
```

This happens through `formatRichText(value)`.

Use backticks for short technical terms only. Do not use raw HTML in JSON content unless a renderer explicitly expects it.

## Stability Rules

- Keep `anchor` values stable.
- Keep menu `anchor` values aligned with `PAGE_PATHS` and wrapper `data-page`.
- Avoid reordering list items if user progress should be preserved.
- Preserve English and Vietnamese file pairs.
- Keep code snippets as valid JSON strings with escaped newlines.
- Prefer adding new fields only after updating renderer code and this schema doc.
