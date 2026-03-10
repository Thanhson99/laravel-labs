# Laravel Labs

This repository is my personal Laravel practice space where I explore multiple approaches and tools in the Laravel ecosystem.

## Learning Goals
- Get hands-on experience with different authentication stacks and starter kits.
- Compare project structure and workflows across setups.
- Keep each practice lab isolated and easy to run independently.

## Included Labs
- `breeze`: basic authentication flow with Laravel Breeze.
- `chirper`: tutorial-style app to practice core CRUD and foundational real-time concepts.
- `jetstream`: advanced auth/team features with Laravel Jetstream.
- `sail`: Docker-based local development workflow with Laravel Sail.

## Quick Start (Example)
Using `breeze` as an example (same idea for `chirper`, `jetstream`, `sail`):

```bash
cd breeze
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

## Notes
- Each lab is a standalone Laravel project.
- Generated dependencies/build artifacts are ignored from the root `.gitignore` to keep the repo clean.
