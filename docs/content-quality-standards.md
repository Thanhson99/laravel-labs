# Content Quality Standards

This document defines professional content rules for Laravel Labs. Read it before writing, rewriting, translating, expanding, or reorganizing learning content.

## Content Mission

Every content change should help a learner build real engineering judgment.

Good content connects:

- concept
- context
- example
- common mistake
- practical use
- tradeoff or production concern when relevant

Avoid content that only sounds technical but does not help the reader decide, debug, build, or explain.

## Voice And Tone

Preferred voice:

- clear
- practical
- direct
- friendly but not childish
- grounded in real Laravel/PHP work
- honest about tradeoffs

Avoid:

- hype
- vague motivation
- filler intros
- overpromising
- academic stiffness
- shallow "best practice" claims without context
- insulting beginners

Good:

```text
Use a Form Request when validation starts to grow or when the same request rules need to be tested clearly.
```

Weak:

```text
Form Requests are very powerful and you should always use them because they are best practice.
```

## Professional Depth Rule

A page should not only answer "what is it?" It should also answer at least some of:

- why it exists
- when to use it
- when not to use it
- how it fails
- how to debug it
- how it appears in a real Laravel project
- how it affects security/performance/maintainability
- what beginners often confuse it with

The more advanced the page, the more it should include failure modes and tradeoffs.

## Content Structure Rule

For substantial topics, prefer this shape:

1. Short definition or framing.
2. Why it matters.
3. Practical example.
4. Common mistakes.
5. Debug/review checklist.
6. Production or team-work implication when relevant.

Do not force every small item into this full structure. Use it when writing a full section or topic.

## Renderer-Friendly Structure Rule

Content should be written so the existing renderer can present it cleanly without one-off CSS fixes.

- Use default card sections for independent related concepts.
- Use `type: "list"` for sequences, checklists, grouped notes, Q&A, and progress-trackable learning items.
- Use `links` sections for references instead of putting bare URLs in prose.
- Use PHP `modules` or `phases` for PHP level content so related title, description, bullets, examples, and questions stay inside the designed white blocks.
- Keep parent titles concise and descriptive; put explanation in `body`, `description`, `intro`, or `summary`.
- Keep nested bullets as supporting details, not full standalone sections.
- Avoid raw HTML, inline style instructions, visual spacer text, or fake headings inside content fields.
- If new content looks like it needs a new visual layout, update the shared renderer/component docs and CSS rather than patching a single page.

## Bilingual Quality Rules

The portal uses English and Vietnamese.

When changing content:

- keep `.en.json` and `.vi.json` aligned by meaning
- keep item counts and order aligned when the files are paired
- keep anchors stable across languages
- translate ideas, not word-by-word grammar
- preserve common technical terms when translation would make them harder to recognize
- avoid mixing too much English into Vietnamese unless it is a known technical term
- do not "fix" Vietnamese based only on PowerShell mojibake

Vietnamese style:

- natural developer Vietnamese
- practical, direct wording
- keep terms like `request`, `response`, `route`, `controller`, `service`, `repository`, `migration`, `queue`, `job`, `cache`, `deploy` when they are commonly used in teams
- explain the term if it is beginner-facing

English style:

- concise technical English
- avoid unnecessary idioms
- prefer plain verbs
- avoid overly formal textbook tone

## Examples And Code Snippet Rules

Examples should be:

- short enough to read
- directly connected to the concept
- safe by default
- valid for the framework/version being discussed
- not filled with unrelated setup

Every code example should make clear:

- what problem it demonstrates
- what the important line is
- what mistake it avoids, if relevant

Avoid examples that:

- hardcode secrets
- concatenate user input into SQL
- skip validation in a way learners may copy
- hide authorization issues
- use outdated Laravel patterns without saying why
- are too large for a learning card

If the snippet is intentionally incomplete, say so in the surrounding text.

## Laravel Content Rules

Laravel content should emphasize system flow:

- route
- middleware
- request validation
- controller
- service/action
- model/query/repository
- view/resource/response
- queue/event/job when asynchronous

When explaining a Laravel feature, include real project context:

- where the code lives
- what file a learner should open first
- what can go wrong
- how to test or debug it

Avoid teaching Laravel as disconnected commands only.

## PHP Content Rules

PHP content should support Laravel understanding.

Good PHP explanations connect syntax to:

- request/response behavior
- arrays and data flow
- functions and scope
- OOP and dependency injection
- errors/exceptions
- database access
- file handling
- testing

Avoid turning PHP content into pure language trivia unless it helps Laravel/backend work.

## Interview Content Rules

Interview answers should be role-appropriate.

Fresher:

- clear definition
- simple example
- avoid over-engineering

Junior:

- basic tradeoff
- common mistakes
- simple debugging angle

Intermediate:

- implementation details
- testing and maintainability
- security/performance awareness

Senior:

- architecture
- team impact
- production failure modes
- migration and long-term maintenance

Master:

- system design
- organizational tradeoffs
- risk management
- scaling decisions

DevOps:

- deployment
- environment
- observability
- rollback
- security
- runtime operations

Good interview answer shape:

1. Direct answer.
2. Short explanation.
3. Example.
4. Common mistake or tradeoff.

Avoid:

- memorized one-liners
- huge essays for fresher-level questions
- unsupported claims like "always use X"

## Glossary Rules

Glossary entries should be short but useful.

Good glossary entry includes:

- plain definition
- why it matters
- confused-with note when relevant
- link or reference to deeper topic when useful

Avoid:

- dictionary-only definitions
- long tutorial sections inside glossary
- unexplained acronyms

## Practice Content Rules

Practice tasks must be actionable.

Each task should clarify:

- what to build or inspect
- what files/concepts are involved
- what success looks like
- optional extension if useful

Hub Laravel UI copy should stay English-only unless a page explicitly supports bilingual switching. Do not use Vietnamese without accents in Blade views, config-driven labels, or generated practice payloads.

Good practice task:

```text
Add a `message` column to chirps, create a Form Request for validation, and write a feature test that a logged-in user can create a chirp.
```

Weak practice task:

```text
Practice CRUD.
```

## Vibe Coding Content Rules

AI-assisted coding content should never encourage blind trust.

Include:

- scope control
- prompt clarity
- review steps
- runtime checks
- hallucination risk
- security and data safety
- test/verification expectations

Good Vibe Coding content teaches how to use AI while keeping engineering ownership.

## Roadmap Content Rules

Roadmap content should orient the learner.

Good roadmap branch:

- tells what the topic is
- explains why it comes at this stage
- lists concrete subtopics
- points to references or next pages

Avoid making roadmap branches as detailed as full topic pages.

## Source And Reference Rules

Use references when:

- linking official docs
- pointing to standards
- explaining a tool/library behavior that may change
- citing external learning resources

Prefer official or primary sources for technical facts.

Do not add random links just to fill a reference list.

## Consistency Rules

Keep naming consistent:

- Laravel
- PHP
- Blade
- Eloquent
- Composer
- Artisan
- Vite
- Sanctum
- Fortify
- Jetstream
- Livewire
- Sail

Use the same term across related pages unless there is a reason to introduce a synonym.

## Content Review Checklist

Before finishing content work, check:

- Is the content in the right learning area?
- Does it match the target level?
- Does it give practical value?
- Are EN and VI aligned?
- Are anchors stable?
- Are links valid?
- Are code snippets safe and relevant?
- Does it avoid raw HTML unless the renderer expects it?
- Does it preserve JSON validity?
- Does it avoid changing progress IDs without reason?

## Publish Quality Bar

Content is ready when a learner can answer:

- What is this?
- Why does it matter?
- Where do I see it in a Laravel/PHP project?
- What mistake should I avoid?
- How can I practice or verify it?

If the content cannot answer at least the relevant parts of those questions, it needs another pass.
