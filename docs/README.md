# Laravel Labs Documentation Index

This folder contains AI onboarding, engineering rules, content rules, and maintenance workflows for Laravel Labs.

## Start Here

Always read these first:

1. `../AGENTS.md`
2. `ai-context.md`
3. `rule-precedence.md`
4. `definition-of-done.md`
5. The task-specific docs below.

## Task-Based Reading Guide

For static site rendering, navigation, search, progress, or shell behavior:

- `static-site-architecture.md`
- `content-map.md`
- `data-schema.md`
- `technical-debt.md`

For JSON learning content:

- `content-taxonomy.md`
- `content-quality-standards.md`
- `data-schema.md`
- `content-map.md`

For CSS, UI, layout, or responsive behavior:

- `design-system.md`
- `static-site-architecture.md`
- `content-map.md`

For Laravel code generation/refactor/review:

- `principal-laravel-architect-rules.md`
- `laravel-coding-standards.md`
- `engineering-standards.md`
- `laravel-labs-inventory.md`
- `templates.md`

For setup, testing, or verification:

- `local-setup-and-verification.md`
- `laravel-labs-inventory.md`
- `ai-review-checklist.md`

For known issues:

- `technical-debt.md`
- `ai-change-log.md`

For reusable workflows:

- `ai-workflows.md`
- `templates.md`

For durable architecture/process decisions:

- `adr-guide.md`
- `templates.md`

## Documentation Files

- `ai-context.md`: repository mental model and first checks.
- `content-map.md`: page keys, paths, shells, menus, data files, and content map.
- `static-site-architecture.md`: runtime rendering behavior.
- `data-schema.md`: JSON content contracts.
- `content-taxonomy.md`: learning-area boundaries.
- `content-quality-standards.md`: professional writing and bilingual quality rules.
- `design-system.md`: CSS/UI style and component rules.
- `local-setup-and-verification.md`: how to run and verify locally.
- `laravel-labs-inventory.md`: per-Laravel-lab inventory.
- `principal-laravel-architect-rules.md`: highest-priority Laravel architecture/output rules.
- `laravel-coding-standards.md`: Laravel layering and code organization rules.
- `engineering-standards.md`: cross-cutting engineering standards.
- `rule-precedence.md`: conflict resolution between docs.
- `definition-of-done.md`: quality gates by task type.
- `adr-guide.md`: architecture decision record guidance.
- `templates.md`: reusable output templates for AI/team work.
- `ai-workflows.md`: repeatable maintenance workflows.
- `ai-review-checklist.md`: review checklist.
- `technical-debt.md`: known issues and recommended fix order.
- `ai-change-log.md`: durable findings and doc updates.

## Maintenance Rule

When a durable rule is added, update:

- `../AGENTS.md` if every AI session must know it.
- `README.md` if a new doc file is added.
- `rule-precedence.md` if the rule can conflict with existing guidance.
- `ai-change-log.md` with a short dated note.
