# Laravel Coding Standards

This document defines how Laravel application code should be structured in this repository. Read it before writing or reviewing code under `breeze/`, `chirper/`, `jetstream/`, or `sail/`.

## Core Principle

Keep each layer responsible for one kind of work:

- Routes map URLs to controllers or framework actions.
- Form Requests validate and authorize input.
- Controllers orchestrate the request and response.
- Services or Actions hold business logic.
- Repositories or Query classes hold reusable database access/query logic.
- Models define data shape, relationships, casts, scopes, and small domain helpers.
- Jobs handle background work.
- Events describe something that happened.
- Listeners react to events.
- Policies/Gates decide permissions.
- Resources/ViewModels/DTOs shape output when needed.

Do not put everything in controllers. Do not hide business logic inside Blade views. Do not create abstractions before the feature is complex enough to need them.

## Recommended Folder Structure

Use the existing app's structure first. If the app does not already have these folders and the feature needs them, prefer:

```text
app/
  Actions/
    FeatureName/
  Services/
    FeatureName/
  Repositories/
    Contracts/
    Eloquent/
  DTOs/
  Queries/
  Data/
  Enums/
  Exceptions/
  Http/
    Controllers/
    Requests/
    Resources/
  Jobs/
  Events/
  Listeners/
  Models/
  Policies/
```

Do not add all folders upfront. Add only what the current feature needs.

## Route File Rules

Route files should stay small, searchable, and grouped by product area.

Rules:

- Keep `routes/web.php` and `routes/api.php` as entry points when an app has many routes.
- Split large route maps into `routes/web/*.php` and `routes/api/*.php` by feature area or workflow.
- Name route files by responsibility, for example `learning.php`, `practice-content.php`, `practice-labs.php`, and `practice-catalog.php`.
- Preserve route names, URLs, middleware, and route ordering when splitting files.
- Put generic parameter routes such as `/practice/{exercise}` after explicit routes in the same URL family.
- Use route groups for shared prefix, name, middleware, and domain rules.
- Add a short English file header after imports that explains what the route file exposes.
- Add one short English comment per route group that explains the workflow or URL family.
- In learning/practice apps, add one short English comment above each route because route files double as a learner-facing map of available exercises.
- Keep route comments factual and concise. Start route-purpose comments with a clear verb such as `Show`, `Return`, `Store`, `Load`, `Open`, `Normalize`, `Evaluate`, or `Summarize`.
- Do not put business rules, implementation details, or controller/service behavior in route comments.
- Add or update a route-documentation test when route comment rules become stricter, so the rule is enforced automatically.

## Folder Responsibility Rules

Every folder must have a clear responsibility. Do not place files based only on convenience.

- `routes/`: URL mapping only. No business logic, queries, request parsing workflows, or response formatting beyond route grouping.
- `app/Http/Controllers/`: thin request/response orchestration only.
- `app/Http/Requests/`: validation, request authorization, and small input normalization.
- `app/Http/Resources/`: API response shaping when response structures are reused or public.
- `app/Services/`: business workflows, orchestration, and domain decisions.
- `app/Actions/`: single-purpose commands or Jetstream/Fortify-style operations.
- `app/Repositories/` and `app/Queries/`: persistence and reusable read/query logic.
- `app/Models/`: relationships, casts, fillable fields, scopes, and small model helpers.
- `resources/views/`: presentation only. Prepare complex data before rendering.
- `config/`: stable configuration, constants, and catalog-like definitions that do not belong in code flow.
- `tests/Feature/`: route, HTTP, validation, authorization, and rendered behavior.
- `tests/Unit/`: pure services, value objects, helpers, and domain rules.

When a folder starts mixing responsibilities, split by feature area first, then introduce a new layer only when it removes real complexity.

## Controller Rules

Controllers should be thin.

Allowed in controllers:

- accept a request
- call authorization
- call a Form Request
- call one service/action method
- return redirect/view/json/resource response
- choose HTTP status codes

Avoid in controllers:

- raw SQL
- long query chains repeated in multiple methods
- business rules
- payment/API workflows
- file processing
- transaction orchestration for complex writes
- loops that mutate many records
- email/notification construction

Example:

```php
final class ChirpController extends Controller
{
    public function store(StoreChirpRequest $request, CreateChirpService $service): RedirectResponse
    {
        $service->create($request->user(), $request->validated());

        return redirect()
            ->route('dashboard')
            ->with('status', 'Chirp created.');
    }
}
```

## Form Request Rules

Use Form Requests when validation is more than trivial or reused.

Put this in Form Requests:

- validation rules
- authorization for this request shape
- custom validation messages when useful
- small normalization through `prepareForValidation` when needed

Do not put business workflows in Form Requests.

Example:

```php
final class StoreChirpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:255'],
        ];
    }
}
```

## Service Rules

Use services for business logic and use-case orchestration.

Good service responsibilities:

- create/update/delete flows
- coordinate multiple repositories/models
- apply business rules
- handle transactions
- call external API clients
- dispatch jobs/events after successful writes

Services should not know about Blade, redirect responses, or HTTP-specific response formatting.

Example:

```php
final class CreateChirpService
{
    public function __construct(
        private readonly ChirpRepository $chirps,
    ) {
    }

    public function create(User $user, array $data): Chirp
    {
        return DB::transaction(function () use ($user, $data) {
            return $this->chirps->createForUser($user, [
                'message' => $data['message'],
            ]);
        });
    }
}
```

## Action Class Rules

Use an Action class when the app already follows Jetstream/Fortify style or the operation is a single clear command.

Good action names:

- `CreateTeam`
- `InviteTeamMember`
- `DeleteUser`
- `PublishPost`
- `SyncExternalOrder`

Actions can replace services for focused use cases. Do not create both a service and an action for the same simple flow unless there is a clear reason.

## Repository Rules

Use repositories or query classes when query/database access becomes reusable or complex.

Good repository responsibilities:

- reusable Eloquent queries
- persistence methods
- query composition
- hiding storage-specific details from services

Avoid repositories when:

- the feature is a single simple `Model::create()` call
- no query is reused
- the abstraction only forwards every Eloquent method without adding meaning

Example:

```php
final class ChirpRepository
{
    public function createForUser(User $user, array $attributes): Chirp
    {
        return $user->chirps()->create($attributes);
    }

    public function latestForFeed(int $limit = 30): Collection
    {
        return Chirp::query()
            ->with('user:id,name')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
```

Do not write SQL in controllers. If raw SQL is genuinely needed, keep it in a repository/query class and explain why Eloquent/query builder is not enough.

## Query Class Rules

Use query classes for read-heavy screens with filters/sorting/search.

Example names:

- `ChirpFeedQuery`
- `UserSearchQuery`
- `TeamMemberQuery`

Query classes should return Eloquent builders, collections, paginators, or DTOs depending on the screen.

## Model Rules

Models should define:

- `$fillable` or guarded strategy
- casts
- relationships
- scopes
- small computed helpers

Models should not become god classes. Avoid putting large business workflows, external API calls, and request-specific behavior in models.

Example:

```php
final class Chirp extends Model
{
    protected $fillable = [
        'message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

## Validation And Authorization

Validation belongs in Form Requests when possible.

Authorization belongs in:

- Form Request `authorize()` for request-level checks
- policies for model permissions
- gates for simple global abilities

Do not hide permission checks in Blade only. UI checks are not security.

## Transaction Rules

Use `DB::transaction()` when a flow writes multiple records or combines a write with dependent side effects.

Good transaction cases:

- create order and order lines
- update balance and write ledger entry
- create team and membership
- delete user-owned records consistently

Avoid doing slow external HTTP calls inside a transaction. Call external services before or after the transaction depending on consistency needs, and document the decision when it is non-obvious.

## Exception Rules

Use exceptions for exceptional states, not normal control flow.

Prefer domain-specific exceptions when the service layer needs to communicate a business failure:

```php
final class ChirpLimitExceeded extends RuntimeException
{
}
```

Controllers should convert exceptions to user-facing responses only when the exception is expected at that boundary. Unexpected exceptions should go through Laravel's exception handler.

## External API Rules

External API code should not live in controllers.

Use:

- `app/Services/VendorName`
- `app/Clients/VendorName`
- config values from `config/services.php`
- typed methods for each operation
- timeouts
- error handling
- tests with fakes/mocks

Do not hardcode secrets. Use `.env.example` for documenting required environment keys.

## SQL And Database Rules

Prefer Eloquent or query builder for normal app logic.

Use raw SQL only when:

- query builder cannot express the query cleanly
- performance requires a specific SQL construct
- database-specific feature is intentional

Raw SQL rules:

- keep it out of controllers
- parameterize bindings
- document why raw SQL is used
- add tests around the behavior

Never concatenate user input into SQL strings.

## DTO And Data Shape Rules

Use DTOs when arrays become unclear across layers.

Good DTO cases:

- external API payloads
- complex service input
- report/filter parameters
- immutable command data

Avoid DTOs for tiny one-off flows where a validated array is clear.

## Comment Rules

Write comments only when they explain why, not what.

Good comments:

```php
// Keep the lock short: the external sync runs after commit.
```

Bad comments:

```php
// Create a chirp.
$chirp = Chirp::create($data);
```

Use comments for:

- non-obvious business rules
- concurrency/locking decisions
- raw SQL reason
- transaction boundaries
- external API quirks
- temporary compatibility behavior

Remove stale comments when behavior changes.

## Docblock Rules

Do not add docblocks that repeat obvious PHP types.

Avoid:

```php
/**
 * Store a chirp.
 */
public function store(StoreChirpRequest $request): RedirectResponse
```

Use docblocks when they add value:

- array shape
- generic collection type
- complex return structure
- domain rule that cannot be expressed in type hints
- temporary compatibility note

Examples:

```php
/**
 * @param array{message: string} $data
 */
public function create(User $user, array $data): Chirp
```

```php
/**
 * @return Collection<int, Chirp>
 */
public function latestForFeed(int $limit = 30): Collection
```

Prefer native PHP types whenever possible.

## Naming Rules

Use clear names that describe intent.

Controllers:

- `ChirpController`
- `TeamInvitationController`

Requests:

- `StoreChirpRequest`
- `UpdateProfileRequest`

Services:

- `CreateChirpService`
- `UpdateTeamMembershipService`

Actions:

- `CreateTeam`
- `DeleteUser`

Repositories:

- `ChirpRepository`
- `TeamRepository`

Query classes:

- `ChirpFeedQuery`
- `UserSearchQuery`

Jobs:

- `SendTeamInvitationEmail`
- `SyncOrderToCrm`

Events:

- `ChirpCreated`
- `TeamMemberInvited`

Policies:

- `ChirpPolicy`
- `TeamPolicy`

## Test Rules

Every behavior change should have a test when practical.

Use Feature tests for:

- routes
- auth/authorization
- validation
- redirects
- JSON responses
- database effects

Use Unit tests for:

- pure services
- domain helpers
- value objects
- small algorithms

For database behavior:

- assert database records
- assert authorization boundaries
- assert validation errors
- test both success and important failure paths

Do not only test implementation details. Test behavior.

## Blade Rules

Blade should render UI, not own business logic.

Allowed in Blade:

- conditionals for display
- loops over prepared data
- calls to policies/gates for UI visibility
- components/partials

Avoid in Blade:

- database queries
- complex calculations
- business workflows
- hidden security logic

If a view needs complex data, prepare it in a controller, query class, view model, or component.

## Practical Layering Example

For a feature like Chirps:

```text
routes/web.php
  -> ChirpController
    -> StoreChirpRequest
    -> CreateChirpService
      -> ChirpRepository
        -> Chirp model / SQL
```

Read flow:

```text
Controller receives request
Form Request validates and authorizes
Controller calls Service
Service applies business rule and transaction
Repository persists/query data
Controller returns response
```

This is the default direction for non-trivial Laravel features in this repo.
