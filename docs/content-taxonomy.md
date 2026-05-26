# Content Taxonomy

This document explains the learning areas and content intent of Laravel Labs. Read it before adding, moving, or rewriting learning content.

## Product Purpose

Laravel Labs is a structured learning portal for:

- PHP foundations
- Laravel depth
- interview preparation
- practical exercises
- glossary/reference material
- AI-assisted coding workflows
- real Laravel lab projects

The content should connect fundamentals, real-world engineering, failure modes, tradeoffs, and practical examples. It should not become a random note dump.

## Main Learning Areas

### Learning Tree / Roadmap

Page key:

- `roadmap`

Wrapper:

- `sites/roadmap/index.html`

Content file:

- `data/site-content.en.json`
- `data/site-content.vi.json`

Purpose:

- Give learners a visual map before they enter PHP/Laravel details.
- Explain learning order and topic relationships.
- Use `mindmap` section type.

Content style:

- broad concepts
- learning order
- references
- branch notes
- not too implementation-heavy

### PHP

Page keys:

- `php`
- `php-starter`
- `php-intermediate`
- `php-advanced`

Wrappers:

- `sites/php/index.html`
- `sites/php/starter.html`
- `sites/php/intermediate.html`
- `sites/php/advanced.html`

Data files:

- `data/php/starter.*.json`
- `data/php/intermediate.*.json`
- `data/php/advanced.*.json`

Purpose:

- Build the PHP base needed to understand Laravel source code.
- Move from syntax and data flow toward architecture, testing, integration, and production thinking.

Content style:

- practical PHP concepts
- short examples
- questions and answers
- keyword-rich inline terms using backticks
- enough depth to support Laravel learning

### Laravel

Page keys:

- `laravel`
- `laravel-*`

Wrappers:

- `sites/laravel/index.html`
- `sites/laravel/*.html`

Data files:

- `data/laravel/*.{en,vi}.json`

Purpose:

- Teach Laravel as a connected system, not isolated snippets.
- Cover structure, tooling, request flow, data, auth, API integration, files, queues, performance, realtime, quality, DevOps, maintenance, and repo mapping.

Current Laravel topics:

- Overview
- App structure
- Composer & vendor
- PHP & Artisan commands
- NPM & frontend
- Blade & UI
- Database & Eloquent
- Request flow
- Container & architecture
- Auth & security
- API & integration
- Files & uploads
- Queues & integrations
- Performance & search
- Realtime
- Testing & debugging
- Sail & deployment
- Upgrade & maintenance
- Repo mapping

Content style:

- applied explanations
- real failure modes
- production concerns
- code snippets where useful
- tips and notes for practical judgment

### Interview

Page keys:

- `interview`
- `interview-fresher`
- `interview-junior`
- `interview-intermediate`
- `interview-senior`
- `interview-master`
- `interview-devops`

Data files:

- `data/interview/*.{en,vi}.json`

Purpose:

- Prepare across seniority levels.
- Cover short theory, applied scenarios, communication, ownership, debugging, architecture, and DevOps thinking.

Content style:

- question/answer format
- progressive seniority
- practical framing
- concise enough for review
- no shallow memorization if a real explanation is needed

### Vibe Coding

Page keys:

- `vibe-coding`
- `vibe-prompting`
- `vibe-ai-crud`
- `vibe-ai-review`
- `vibe-ai-runtime`

Data files:

- `data/vibe-coding/*.{en,vi}.json`

Purpose:

- Teach AI-assisted coding with engineering discipline.
- Emphasize prompting, scoped generation, review, safe runtime changes, and production guardrails.

Content style:

- practical AI workflows
- review discipline
- scope control
- hallucination awareness
- runtime safety

### Glossary

Page key:

- `glossary`

Content location:

- `data/site-content.*.json`

Purpose:

- Define terms across web, Laravel, database, runtime, DevOps, architecture, and AI-assisted coding.
- Help beginners decode technical language.

Content style:

- short definitions
- common confusions
- links to deeper areas when possible
- avoid overly academic definitions

### Practice

Page key:

- `practice`

Content location:

- `data/site-content.*.json`

Purpose:

- Turn passive reading into action.
- Provide mini projects, bug labs, review checklists, study rhythms, and next steps.

Content style:

- task-oriented
- concrete
- connected to the Laravel lab folders
- suitable for repeated practice

## Content Depth Rules

Beginner content should:

- explain terms plainly
- show small examples
- avoid hidden assumptions
- connect syntax to real behavior

Intermediate content should:

- discuss structure and tradeoffs
- include debugging paths
- show common mistakes
- connect multiple layers of the stack

Advanced content should:

- discuss production behavior
- include failure modes
- mention security, performance, and maintainability
- emphasize judgment and system boundaries

## Bilingual Rules

The portal supports English and Vietnamese. When adding durable content:

- update `.en.json` and `.vi.json` together when both exist
- keep the same anchors and item order unless intentionally changing behavior
- do not remove Vietnamese text because terminal output looks garbled
- check Vietnamese strings for button/card overflow
- preserve technical terms when translating would make the term harder to recognize

## Cross-Linking Rules

Use internal links when content naturally points to another area:

- PHP basics -> PHP pages
- Laravel concepts -> Laravel topic pages
- interview explanations -> related Laravel/PHP pages
- AI review practices -> Vibe Coding AI Review
- lab practice -> Practice and Repo mapping

Prefer page keys and `getPageHref()` in JavaScript. In JSON `href` fields, use paths that work from the rendered page context or are already supported by existing render behavior.

## Content Anti-Patterns

Avoid:

- dumping unrelated notes into one section
- adding a topic without English/Vietnamese pair
- adding a menu item without a wrapper and data file
- moving content to a different learning area without updating navigation/search assumptions
- turning interview answers into memorized one-liners when the concept needs explanation
- adding raw HTML in JSON
- changing stable anchors casually

## How To Decide Where New Content Belongs

- If it teaches PHP syntax, runtime, OOP, or backend basics: PHP.
- If it teaches Laravel framework behavior: Laravel.
- If it is a question/answer by role level: Interview.
- If it teaches prompting, AI generation, AI review, or AI runtime safety: Vibe Coding.
- If it defines a term briefly: Glossary.
- If it asks the learner to build, debug, or review something: Practice.
- If it orients learning order across technologies: Roadmap.
- If it explains the actual repo/lab folders: Laravel Repo Map or docs.
