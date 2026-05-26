# Rule Precedence

This document explains how to resolve conflicts between documentation files.

## Precedence Order

When rules conflict, apply them in this order:

1. Direct user instruction in the current conversation.
2. Safety/security requirements.
3. `AGENTS.md`.
4. `docs/principal-laravel-architect-rules.md` for Laravel code generation/refactor/review.
5. `docs/engineering-standards.md` for cross-cutting technical behavior.
6. `docs/laravel-coding-standards.md` for Laravel organization details.
7. `docs/laravel-labs-inventory.md` for app-specific version/structure facts.
8. `docs/static-site-architecture.md` for static site runtime behavior.
9. `docs/data-schema.md` for JSON structure.
10. `docs/content-quality-standards.md` for writing quality.
11. `docs/content-taxonomy.md` for learning-area placement.
12. `docs/design-system.md` for UI/CSS behavior.
13. `docs/ai-workflows.md` and `docs/ai-review-checklist.md` for process.
14. `docs/technical-debt.md` and `docs/ai-change-log.md` for historical context.

## Known Conflict: PHPDoc

`docs/laravel-coding-standards.md` says docblocks should be used when they add value. `docs/principal-laravel-architect-rules.md` says all generated Laravel classes and methods/functions must include professional PHPDoc.

Resolution:

- For new AI-generated Laravel application code, `principal-laravel-architect-rules.md` wins.
- For editing existing code, follow the local file style unless the user asks to enforce Principal standards.
- For static-site JavaScript/CSS/docs, do not apply PHPDoc rules.

## Known Conflict: Architecture Strictness

Some lab apps are scaffold/demo projects and do not currently use full enterprise layering.

Resolution:

- For small bug fixes, follow the existing app style.
- For new non-trivial Laravel features, use the Principal architecture rules.
- Do not create every folder upfront. Add only the layers needed by the feature.
- Do not force repository/service abstractions onto a one-line read-only change unless the feature is growing.

## Known Conflict: YAGNI vs Enterprise Layers

The repo values YAGNI, but also asks for enterprise-grade Laravel structure.

Resolution:

- Use enterprise layers when they clarify responsibility, testability, or future growth.
- Avoid abstractions that only forward calls without adding meaning.
- Prefer Action classes for focused workflows.
- Prefer Services plus Repositories for workflows with business logic and reusable database access.

## Known Conflict: Static Portal vs Laravel Rules

Laravel architecture rules do not apply to the static GitHub Pages portal.

Resolution:

- For `assets/**`, `partials/**`, `sites/**`, and `data/**`, follow static-site docs.
- For `breeze/**`, `chirper/**`, `jetstream/**`, and `sail/**`, follow Laravel docs.

## If Unsure

Use this decision:

- Is it Laravel app code? Read Principal + Laravel + Engineering standards.
- Is it static portal code? Read static architecture + design system + data schema.
- Is it learning content? Read taxonomy + content quality + data schema.
- Is it setup/testing? Read local setup + inventory + review checklist.
