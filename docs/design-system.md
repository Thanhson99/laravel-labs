# Design System And UI Notes

This document captures the current visual language and CSS organization of the static portal. Read it before changing `assets/css/**`, markup structure, render helpers that output UI, or component classes.

## CSS Entry And Module Order

The CSS entry file is:

```text
assets/styles.css
```

It imports modules in this order:

1. `assets/css/01-core.css`
2. `assets/css/02-roadmap-and-layout.css`
3. `assets/css/03-php-and-learning.css`
4. `assets/css/04-content-and-components.css`
5. `assets/css/05-overrides-and-responsive.css`

Respect this order. Put base variables and global primitives in earlier files, specialized components in middle files, and overrides/responsive fixes in later files.

## Visual Direction

The portal is a warm, editorial learning portal, not a SaaS dashboard.

Current design traits:

- warm beige/cream background
- terracotta/orange accent
- teal and amber secondary tones for roadmap states
- large readable learning sections
- card components for repeated content
- line-separated full-width panels for major page sections
- dark mode support via CSS variables
- rounded components, but page sections themselves are not floating cards
- dense enough for study, but still comfortable for long-form reading

Do not convert the site into a generic admin UI or marketing landing page.

## Theme Variables

Core variables live in `:root` and `body[data-theme="dark"]`.

Important variables:

- `--bg`
- `--surface`
- `--surface-strong`
- `--text`
- `--muted`
- `--accent`
- `--accent-deep`
- `--line`
- `--shadow`
- `--radius-lg`
- `--radius-md`
- `--radius-sm`
- `--max-width`
- `--code-bg`
- `--code-text`

When adding UI, use these variables instead of hardcoding new palettes.

## Layout Rules

Global width:

```css
--max-width: 1440px;
```

Main layout containers:

- `.site-header`
- `.hero`
- `main`
- `.detail-main`

Major sections use `.panel`.

Current `.panel` style:

- top border
- transparent background
- no card shadow
- no outer card frame

Do not wrap every page section in a decorative card. Cards are reserved for repeated items, compact controls, search results, roadmap nodes, and similar framed units.

## Header And Navigation

Important classes:

- `.site-header`
- `.site-header-shell`
- `.brand-link`
- `.site-nav`
- `.site-nav-link`
- `.site-nav-dropdown`
- `.site-nav-trigger`
- `.site-nav-submenu`
- `.site-nav-submenu-link`
- `.site-header-actions`
- `.header-search-toggle`
- `.lang-switch`
- `.theme-toggle`

Navigation uses hover/active animated backgrounds. Dropdown submenus have fixed card-like grid sizing on desktop and single-column behavior on mobile.

When adding nav items:

- check long Vietnamese labels
- check desktop dropdown grid
- check mobile dropdown width
- check active state
- avoid making submenu cards taller by accident

## Core Components

Repeated card classes:

- `.service-card`
- `.repo-card`
- `.content-card`
- `.link-card`
- `.level-nav-card`
- `.php-level-card`
- `.qa-card`
- `.snippet-card`

Buttons/links:

- `.btn`
- `.back-link`
- `.text-link`
- `.php-resume-link`

Progress components:

- `.page-utility-bar`
- `.section-progress-pill`
- `.content-progress-toggle`
- `.reading-dock`

Search:

- `.global-search`
- `.global-search-panel`
- `.global-search-result`

Roadmap:

- `.roadmap-tree-panel`
- `.roadmap-filter-bar`
- `.roadmap-overview`
- `.roadmap-tree`
- `.roadmap-node-card`
- `.roadmap-chip`
- `.roadmap-reference-link`

PHP learning:

- `.php-roadmap`
- `.php-resume-card`
- `.php-phase`
- `.php-note-block`
- `.php-example-block`
- `.php-question-block`
- `.php-keyword-*`
- `.inline-keyword`

Interview/list content:

- `.bullet-list`
- `.interview-qa-list`
- `.bullet-partitions`
- `.bullet-part-header`
- `.bullet-code-shell`
- `.bullet-link-chip`

## Dark Mode

Dark mode is applied via:

```css
body[data-theme="dark"]
```

Any new component with a bright surface should be checked in dark mode. Use existing dark-mode patterns rather than new colors.

Typical dark-mode adjustments:

- darker translucent backgrounds
- softer borders using accent opacity
- reduced bright shadows
- code blocks already use `--code-bg` and `--code-text`

## Responsive Behavior

Important breakpoints observed:

- `920px` for roadmap layout collapse
- `820px` for resume card layout
- `760px` for header, dropdowns, hero padding, interview list padding
- `640px` for compact roadmap/card behavior

Before finalizing UI changes, check:

- mobile header wraps cleanly
- dropdowns stay inside viewport
- buttons do not overflow Vietnamese text
- search modal fits narrow screens
- roadmap tree collapses to one column
- interview progress buttons do not overlap content

## Accessibility Notes

Existing patterns include:

- `.sr-only`
- `aria-expanded` on nav dropdown triggers
- `aria-pressed` on toggles
- `aria-current="page"` on breadcrumbs
- dialog role for global search

When adding controls:

- use real buttons for actions
- use links for navigation
- update `aria-pressed` for toggles
- keep focus states visible
- keep labels or screen-reader labels for icon-like controls

## Rendering And CSS Coupling

Many class names are generated in `assets/site.js`. Before renaming a CSS class, search in:

```powershell
rg -n "class-name" assets partials sites
```

Do not remove a class only because it is not present in static HTML; it may be emitted by JavaScript render helpers.

## Design Guardrails

- Preserve the bilingual layout.
- Avoid creating a new color system.
- Avoid adding a new UI library.
- Avoid nested card-in-card layouts unless an existing component already requires it.
- Prefer extending existing component classes before inventing new ones.
- Keep long-form learning content easy to scan.
- Keep code blocks readable and horizontally safe.
- Preserve dark mode when changing surfaces.
- Check both English and Vietnamese text lengths.
