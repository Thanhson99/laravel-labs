# Engineering Standards

This document contains cross-cutting engineering rules for Laravel Labs. Read it before changing database structure, APIs, security behavior, tests, logging, dependencies, environment files, performance-sensitive code, or Git workflow.

## General Engineering Rules

- Prefer simple code first, but do not mix unrelated responsibilities.
- Follow the existing structure of the target app before introducing new folders.
- Make changes small enough to review.
- Do not refactor unrelated code while implementing a feature.
- Do not hide behavior changes inside formatting-only edits.
- If a change affects public behavior, add or update tests when practical.
- If a tradeoff is non-obvious, document it in code, tests, or docs.
- Treat this repo as multiple projects: the static portal plus four independent Laravel apps.

## Database And Migration Rules

Migrations must be clear, reversible, and safe.

Migration rules:

- Use meaningful table and column names.
- Use foreign keys when the relationship should be enforced.
- Add indexes for lookup columns, foreign keys, and frequent filters.
- Do not add nullable columns by default unless null is a valid domain state.
- Use sensible defaults only when the default has real business meaning.
- Keep destructive migrations explicit and documented.
- Do not drop columns/tables casually.
- Make `down()` reverse the `up()` operation when possible.
- For large production-like tables, prefer staged migrations over one risky migration.

Recommended column conventions:

- primary key: `id`
- foreign key: `{model}_id`, for example `user_id`
- timestamps: `created_at`, `updated_at`
- soft delete: `deleted_at`
- status fields: consider enums or documented string constants

Example:

```php
Schema::create('chirps', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('message', 255);
    $table->timestamps();

    $table->index(['user_id', 'created_at']);
});
```

Avoid:

```php
$table->text('data'); // unclear catch-all
$table->string('status')->nullable(); // unclear domain state
```

## Query And Performance Rules

- Avoid N+1 queries. Use eager loading when displaying related data.
- Paginate lists that can grow.
- Do not load entire tables into memory.
- Use `select()` for large read paths when only a few columns are needed.
- Add database indexes when introducing new filters/sorts.
- Measure or explain performance-sensitive changes.
- Keep expensive work out of request-response flow when it can run as a job.

Common review questions:

- Can this query grow with user data?
- Does this loop query the database?
- Should this endpoint paginate?
- Does this filter need an index?
- Can this write race with another request?

## Transaction And Consistency Rules

Use transactions for multi-step writes.

Good transaction candidates:

- create parent and child records
- update counters and write logs
- move ownership between users/teams
- write a record and related audit entry
- delete or restore related records

Rules:

- Keep transactions short.
- Avoid external HTTP calls inside transactions.
- Avoid dispatching jobs that depend on uncommitted data unless using after-commit behavior.
- If retries can duplicate a request, design the operation to be idempotent.

## API Rules

For JSON APIs:

- Use consistent response shapes.
- Use correct HTTP status codes.
- Return validation errors through Laravel validation.
- Do not leak stack traces or internal exception messages.
- Use API Resources when response shape is reused or public.
- Paginate collection endpoints.
- Version APIs if compatibility matters.

Suggested success shape for custom APIs:

```json
{
  "data": {},
  "message": "Optional message"
}
```

Suggested error shape for expected domain errors:

```json
{
  "message": "Human-readable error",
  "code": "DOMAIN_ERROR_CODE"
}
```

Do not invent a new response format per endpoint.

## Web Controller Response Rules

For Blade/web flows:

- Redirect after successful POST/PATCH/DELETE.
- Use flash messages for user-facing success/failure status.
- Preserve input on validation failure through Laravel defaults.
- Keep route names stable.
- Do not return raw exception text to users.

## Security Rules

Never commit secrets.

Forbidden in code/docs/tests:

- real API keys
- real passwords
- private tokens
- production database credentials
- session cookies
- personal access tokens

Use `.env.example` to document required environment keys with safe placeholder values.

Security checklist:

- Validate all input.
- Authorize all user-owned resource changes.
- Use policies for model permissions.
- Use CSRF protection for web forms.
- Use Sanctum/token middleware for protected APIs.
- Do not trust hidden inputs for ownership or price/role/security decisions.
- Escape output unless the framework/component already guarantees safety.
- Avoid mass assignment vulnerabilities with `$fillable` or a clear guarded strategy.
- Never concatenate user input into SQL.
- Rate limit sensitive endpoints when appropriate.

Sensitive endpoint examples:

- login
- password reset
- OTP/two-factor
- invitation acceptance
- file upload
- webhook
- export/download

## File Upload Rules

For uploads:

- Validate file type and size.
- Do not trust original filenames.
- Store files through Laravel storage disks.
- Keep private files private.
- Do not put user uploads directly into public paths unless intended.
- Scan/transform files when the app domain requires it.
- Store metadata if the app needs traceability.

## Logging Rules

Logs should help debug without leaking secrets.

Log useful context:

- action name
- model IDs
- user ID when relevant
- external provider name
- correlation/request ID if available
- failure reason category

Do not log:

- passwords
- tokens
- full credit card numbers
- secret headers
- full request payloads containing sensitive data

Use log levels intentionally:

- `debug`: local investigation detail
- `info`: important normal lifecycle events
- `warning`: recoverable or suspicious state
- `error`: failed operation needing attention

## Exception And Error Handling Rules

- Let unexpected exceptions bubble to Laravel's handler.
- Catch exceptions when you can add domain context or recover.
- Convert expected domain failures into clear user/API responses.
- Do not swallow exceptions silently.
- Do not return raw exception messages to users.

## Queue And Job Rules

Use jobs for slow or retryable work:

- emails
- notifications
- image processing
- exports
- external sync
- report generation

Job rules:

- Keep payloads small.
- Pass IDs instead of large model graphs when practical.
- Make jobs idempotent if retries are possible.
- Set retry/backoff behavior when failure is expected.
- Log meaningful failure context.
- Avoid assuming request/session state exists in a job.

## Event And Listener Rules

Use events when something happened and multiple reactions may exist.

Good events:

- `UserRegistered`
- `TeamMemberInvited`
- `ChirpCreated`
- `OrderPaid`

Avoid events for simple direct calls where no decoupling is needed.

## Dependency Rules

Do not add dependencies casually.

Before adding a package, check:

- Is it already available in the target app?
- Can Laravel or PHP standard features solve it?
- Is the package maintained?
- Does it add security or operational risk?
- Does it affect build/runtime requirements?

If adding a dependency:

- update only the target app
- commit/update lock files only if the package manager ran
- document usage if it is not obvious
- add tests around package-dependent behavior

## Environment And Config Rules

- Do not commit `.env`.
- Keep `.env.example` safe and current.
- Put app config in `config/*.php`.
- Read environment variables from config files, not deep inside services/controllers.
- Use `config('...')` in application code.
- Document new required env keys.

Good:

```php
'timeout' => env('PAYMENT_TIMEOUT', 10),
```

Then in service:

```php
$timeout = config('services.payment.timeout');
```

Avoid:

```php
$timeout = env('PAYMENT_TIMEOUT');
```

inside application services.

## Testing Strategy

Minimum test expectations:

- validation success and failure
- authorization allowed and denied
- database side effects
- important redirects/responses
- service behavior for non-trivial business logic
- queued jobs/events when relevant

Feature test checklist:

- unauthenticated user behavior
- authenticated user behavior
- unauthorized owner/non-owner behavior
- valid payload
- invalid payload
- expected database changes

Unit test checklist:

- pure business rule
- edge cases
- exception path
- no framework setup unless needed

Do not mock Eloquent heavily in feature tests. Use database tests when verifying persistence.

## Test Naming Rules

Use descriptive test names.

Good:

```php
public function test_user_can_create_a_chirp(): void
```

```php
public function test_user_cannot_update_another_users_chirp(): void
```

Avoid:

```php
public function test_store(): void
```

## Code Style Rules

- Use strict, readable names.
- Prefer early returns for guard clauses.
- Avoid deep nesting.
- Avoid magic strings repeated across files.
- Prefer enums/constants for repeated status values.
- Keep methods short enough to scan.
- Keep constructor dependencies purposeful.
- Do not use global helpers for domain logic unless the project already does.

## Git And Change Management Rules

- Check `git status --short` before editing.
- Do not revert user changes unless explicitly requested.
- Keep unrelated changes out of the same patch.
- Do not run destructive Git commands unless explicitly requested.
- When committing, use focused commits.
- Commit message should describe the actual change, not the tool used.

Suggested commit message style:

```text
docs: add Laravel coding standards
```

```text
fix: correct portal back link resolution
```

```text
feat: add chirp creation flow
```

## Documentation Rules

Update docs when:

- adding new architecture pattern
- adding new environment variable
- adding new setup command
- changing page/content structure
- discovering durable technical debt
- creating a new repeated workflow

Do not document temporary implementation details unless they explain a current limitation or migration path.

## AI-Specific Engineering Rules

When an AI agent changes code:

- read existing files first
- make the smallest coherent change
- avoid inventing new architecture when local patterns exist
- explain any missing verification
- add follow-up notes to `docs/technical-debt.md` or `docs/ai-change-log.md` only when the finding should persist
- never claim tests passed unless they actually ran
