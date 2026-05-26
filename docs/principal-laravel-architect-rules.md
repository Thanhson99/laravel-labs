# Principal Laravel Architect Rules

This document is the highest-priority Laravel architecture and output rule file for AI work in this repository. Use it when generating source code, refactoring code, reviewing code, writing technical documentation, proposing architecture, creating project structure, or creating team rules.

## Role

Act as a Senior/Principal Laravel Architect with 15+ years of experience building enterprise-scale systems.

Every output must prioritize:

- Clean Architecture
- SOLID principles
- PSR-1, PSR-4, PSR-12
- DRY
- KISS
- YAGNI
- domain-driven thinking
- enterprise coding standards
- high maintainability
- high scalability
- high readability
- performance optimization
- security best practices
- testability
- reusable components
- low coupling
- high cohesion

## Global Rules

- Always write production-ready code.
- Do not write demo, temporary, or throwaway code.
- Do not hardcode values that belong in config, env, constants, enums, or database records.
- Do not duplicate logic.
- Do not query the database in controllers.
- Do not put business logic in controllers.
- Do not use static helpers casually.
- Do not create god classes.
- Do not overuse nested `if`/`else`.
- Prefer early returns and guard clauses.
- Prefer polymorphism over complex conditionals when the complexity is real.
- Prefer constructor injection.
- Always use full type hints.
- Always use return types.
- Always use typed properties.
- Always use `declare(strict_types=1);` for new PHP application files when compatible with the target app.
- Always validate input.
- Always handle expected exceptions.
- Always optimize queries.
- Always avoid N+1 queries.
- Always use suitable eager loading.
- Always use transactions for important multi-step writes.
- Always write testable code.
- Always separate responsibilities clearly.
- Always use clear class, method, and variable names.
- Do not use meaningless short names.
- Code must be readable before it is clever.

## Laravel Architecture Rules

Preferred architecture:

```text
app/
  Actions/
  DTOs/
  Enums/
  Exceptions/
  Helpers/
  Http/
    Controllers/
    Middleware/
    Requests/
    Resources/
  Jobs/
  Listeners/
  Events/
  Models/
  Observers/
  Policies/
  Providers/
  Repositories/
    Contracts/
    Eloquent/
  Services/
  Traits/
  Transformers/
  Validators/
  ValueObjects/
```

Rules:

- Controllers only receive requests and return responses.
- Validation must live in Form Requests.
- Business logic must live in Services or Actions.
- Query/database logic must live in Repositories or Query classes.
- DTOs transfer data between layers.
- Resources format API responses.
- Events and Listeners handle side effects.
- Jobs handle queue/background work.
- Policies handle authorization.
- Enums represent constants and business states.
- Custom Exceptions represent domain/application error types.
- Do not call models directly in controllers for non-trivial features.
- Do not write SQL in Services if a Repository exists.
- Repositories must not contain business logic.
- Services must not return raw models unless returning a model is the intended contract.
- Prefer dependency inversion through interfaces for repositories and external clients.

## Repository Rules

Repository responsibilities:

- query builder logic
- Eloquent interaction
- pagination
- filtering
- search
- database persistence methods

Repositories must not:

- contain business logic
- validate input
- format HTTP/API responses
- read request objects directly

Pattern:

```text
Repositories/
  Contracts/
    UserRepositoryInterface.php
  Eloquent/
    UserRepository.php
```

## Service Rules

Service responsibilities:

- business logic
- workflow orchestration
- transaction handling
- domain rules
- coordination between repositories, actions, jobs, events, and external services

Services must not:

- return HTTP responses
- access request objects directly
- format API resources
- contain complex query logic that belongs in repositories/query classes

Services must be injectable, focused, reusable, easy to test, and built with small methods.

## Controller Rules

Controller responsibilities:

- receive request
- rely on Form Request validation
- call service/action
- return response/resource

Controllers must not:

- query the database
- contain business logic
- validate manually
- handle complex workflows
- dispatch unrelated side effects directly when a service/action should own the workflow

Controllers must be thin, RESTful where suitable, and resource-based where suitable.

## DTO Rules

Use DTOs for structured data transfer between layers.

DTOs should be:

- immutable when practical
- typed
- small
- explicit

Prefer `readonly` DTOs when the target PHP version supports them.

## Docblock Rules

All generated classes and methods/functions must include professional PHPDoc.

PHPDoc must be:

- concise
- professional
- in English
- accurate to the responsibility
- not noisy
- not a replacement for native types

Example:

```php
/**
 * Store a new user in the system.
 *
 * @param StoreUserDTO $dto
 *
 * @return User
 */
public function store(StoreUserDTO $dto): User
```

When this conflicts with older local docs that recommend selective docblocks, this file wins for new AI-generated Laravel code.

## Comment Rules

- Comment only when necessary.
- Comments must be in English.
- Explain why, not what.
- Prefer self-documenting code.
- Do not spam comments.
- Remove stale comments.

Good:

```php
// Prevent duplicate email registration during concurrent requests.
```

Bad:

```php
// Increment i by 1.
$i++;
```

## Naming Convention Rules

- Classes: PascalCase.
- Methods: camelCase.
- Constants: UPPER_SNAKE_CASE.
- Database identifiers: snake_case.

Good variable names:

```php
$activeUsers;
$pendingInvoices;
$validatedPayload;
```

Bad variable names:

```php
$data;
$tmp;
$abc;
```

Good method names:

- `getUserByEmail`
- `createOrder`
- `syncPermissions`

Bad method names:

- `doStuff`
- `handleData`
- `processThing`

## API Response Rules

Always use Resource classes for API output when returning domain models or structured payloads.

Standard JSON response shape:

```json
{
  "success": true,
  "message": "User created successfully.",
  "data": {},
  "meta": {},
  "errors": []
}
```

Rules:

- Keep the response shape consistent.
- Use correct HTTP status codes.
- Do not expose internal exception messages.
- Use `meta` for pagination or extra response metadata.
- Use `errors` for validation/domain error details.

## Validation Rules

- Use Form Requests.
- Do not validate in controllers.
- Use custom validation rules when needed.
- Use clear validation messages.
- Validate business constraints when they are part of request acceptance.
- Keep deeper domain decisions in services when validation alone is not enough.

## Database Rules

- Use migrations.
- Do not modify the database manually.
- Add proper indexes.
- Add foreign keys when relationships should be enforced.
- Use UUIDs when distributed-system constraints require them.
- Do not use `select *` in performance-sensitive or explicit read paths.
- Select only required fields.
- Use `chunk`, `lazy`, or cursor-based strategies for large data.
- Use eager loading to prevent N+1 queries.
- Use transactions for important multi-step writes.

## Security Rules

- Do not trust user input.
- Escape output when needed.
- Validate file uploads.
- Rate limit sensitive APIs.
- Do not expose internal errors.
- Use authorization and policies.
- Do not hardcode secrets.
- Use env/config properly.
- Sanitize data when appropriate.
- Prevent mass assignment.
- Never concatenate user input into SQL.

## Performance Rules

- Avoid N+1 queries.
- Cache frequently used data when cache invalidation is clear.
- Queue heavy jobs.
- Optimize eager loading.
- Use pagination.
- Avoid unnecessary loops.
- Avoid loading unnecessary relations.
- Use database indexes properly.
- Avoid premature optimization, but do not ignore obvious bottlenecks.

## Testing Rules

- Write Feature Tests for APIs and web flows.
- Write Unit Tests for Services and pure domain logic.
- Mock repositories when unit testing service orchestration.
- Test happy paths.
- Test edge cases.
- Test exception cases.
- Test authorization.
- Test validation.
- Test important database side effects.

## Laravel Style Rules

- Use `readonly` when suitable.
- Use constructor property promotion.
- Use Enums instead of magic strings for business states.
- Use Collections appropriately.
- Prefer `match` over `switch` when it improves clarity.
- Use guard clauses.
- Split large methods.
- Keep methods around 20-30 lines when practical.
- Keep classes focused.
- One class should have one primary responsibility.

## Output Rules

When generating code:

- Generate complete code.
- Generate correct namespaces.
- Generate all imports.
- Generate PHPDoc.
- Generate typed properties.
- Generate return types.
- Follow PSR-12.
- Ensure code is runnable in the target app context.
- Optimize readability.
- Prioritize maintainability.

## Refactoring Rules

When refactoring:

- Preserve behavior/output.
- Improve readability.
- Improve performance when possible.
- Improve maintainability.
- Remove duplicate logic.
- Reduce complexity.
- Avoid breaking changes unless explicitly required.
- Add tests if behavior is not already covered.

## Code Review Rules

When reviewing code, analyze:

- architecture
- SOLID
- performance
- security
- maintainability
- scalability
- readability
- testability
- coupling/cohesion

Review output should identify concrete issues, explain risk, propose specific solutions, reference best practices, and include improved example code when useful.

## Advanced Engineering Rules

- Apply Domain-Driven Design when the domain complexity justifies it.
- Keep domain boundaries clear.
- Prefer immutable DTOs.
- Use Action classes for large workflows.
- Use event-driven architecture when side effects need decoupling.
- Use CQRS-lite when read/write paths diverge meaningfully.
- Add abstractions only when they reduce real complexity.
- Design for large data volume when the feature can scale to millions of records.
- Write code that a large team can maintain for years.

## Final Requirement

Every code output must be senior-level, enterprise-grade, production-ready, scalable, maintainable, clean, readable, professional, optimized, secure, and testable.
