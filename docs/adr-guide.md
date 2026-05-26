# Architecture Decision Records

Use ADRs for durable technical decisions that future AI/team members should not rediscover.

## When To Write An ADR

Write an ADR when a decision affects:

- architecture boundaries
- framework version strategy
- folder structure
- data model
- API contract
- security model
- deployment/runtime behavior
- testing strategy
- major dependency choice
- long-term content architecture

Do not write ADRs for tiny implementation details.

## Where To Put ADRs

Recommended folder:

```text
docs/adr/
```

File naming:

```text
0001-short-decision-title.md
0002-another-decision.md
```

Use sequential numbers. Do not renumber old ADRs.

## ADR Template

```markdown
# ADR 0001: {Decision Title}

## Status

Proposed | Accepted | Deprecated | Superseded

## Date

YYYY-MM-DD

## Context

What problem are we solving?

## Decision

What did we decide?

## Consequences

What improves?
What gets harder?
What tradeoffs are accepted?

## Alternatives Considered

- Option:
  - Pros:
  - Cons:

## Follow-Up

- Action item:
```

## ADR Quality Rules

- Keep ADRs short.
- Write in English.
- Explain why, not only what.
- Include rejected alternatives when useful.
- Update status instead of deleting old ADRs.
- Link related docs when relevant.

## Current Suggested ADR Candidates

These are not yet ADRs, but may deserve one if the repo evolves:

- Static portal remains JSON-driven instead of using a static site generator.
- Laravel labs remain independent apps instead of a monorepo Laravel workspace.
- Principal Laravel architecture rules apply to new non-trivial Laravel features.
- Full repository/service layering is not forced onto tiny scaffold fixes.
