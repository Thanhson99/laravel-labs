# Templates

Use these templates to keep AI/team output consistent.

## Laravel Feature Implementation Plan

```markdown
## Feature

{short feature name}

## Scope

- In scope:
- Out of scope:

## Architecture

- Route:
- Form Request:
- Controller:
- Service/Action:
- Repository/Query:
- Model changes:
- Policy:
- Resource/View:
- Job/Event:

## Database

- Migration:
- Indexes:
- Foreign keys:
- Transaction needs:

## Tests

- Feature tests:
- Unit tests:
- Authorization tests:
- Validation tests:
- Edge cases:

## Verification

- Commands:
- Manual checks:
```

## Laravel API Endpoint Template

```markdown
## Endpoint

`METHOD /api/...`

## Request

Headers:

```json
{}
```

Body:

```json
{}
```

## Validation

- field:

## Authorization

- Policy/Gate:
- Ownership rules:

## Response

```json
{
  "success": true,
  "message": "",
  "data": {},
  "meta": {},
  "errors": []
}
```

## Failure Cases

- 401:
- 403:
- 404:
- 422:
- 500:
```

## Code Review Template

```markdown
## Findings

1. Severity: High/Medium/Low
   File:
   Issue:
   Risk:
   Fix:

## Architecture

- Controller:
- Service/Action:
- Repository/Query:
- Model:
- Boundaries:

## Security

- Validation:
- Authorization:
- Secrets:
- Input/output safety:

## Performance

- Queries:
- N+1:
- Indexes:
- Pagination:
- Queue/cache:

## Tests

- Existing coverage:
- Missing tests:
- Suggested tests:

## Summary

{short summary}
```

## Technical Documentation Template

```markdown
# {Title}

## Purpose

## Context

## Architecture

## Data Flow

## Key Files

## Configuration

## Security

## Performance

## Testing

## Operational Notes

## Known Limitations
```

## Content Topic Template

```json
{
  "heading": "",
  "intro": "",
  "type": "list",
  "items": [
    {
      "title": "What this concept is",
      "body": "Define it clearly and connect it to real Laravel/PHP work.",
      "bullets": [
        "Why it matters.",
        "Where it appears.",
        "Common mistake."
      ],
      "note": "Optional practical note."
    }
  ]
}
```

## Practice Task Template

```json
{
  "title": "Action-oriented task title",
  "body": "What the learner must build, inspect, or fix.",
  "bullets": [
    "Files or concepts involved.",
    "Success criteria.",
    "Optional extension."
  ],
  "note": "How to verify the result."
}
```

## Commit Message Examples

```text
docs: add principal Laravel architecture rules
docs: document static portal data schema
fix: resolve portal back link paths
feat: add chirp creation workflow
test: cover chirp authorization rules
refactor: move chirp creation logic into service
```
