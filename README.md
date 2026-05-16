# backend

**Purpose:** Catalyst iQ Laravel backend — org GitHub sync, API routes, and (future) Statamic CMS. Sourced from the `cat-iq-dashboard` Laravel Cloud POC.
**Last modified:** 2026-05-16
**Owner / stage:** Catalyst iQ · Stage 5

## Contents

| Path | Type | Description |
|------|------|-------------|
| `app/` | PHP | Livewire UI, GitHub App service, sync jobs, webhooks |
| `routes/` | PHP | Web + webhook routes |
| `database/` | PHP/SQLite | Migrations for repos, milestones, roadmap, wikis |
| `resources/` | Blade/JS/CSS | Flux + Tailwind frontend assets |
| `docs/LARAVEL-CLOUD.md` | Markdown | Deploy, queues, webhooks on Laravel Cloud |

## How to use

Standalone POC repo (Laravel Cloud deploy): [`Catalyst-Internal/cat-iq-dashboard`](https://github.com/Catalyst-Internal/cat-iq-dashboard).

Monorepo copy (this tree) — local dev:

```bash
cd apps/backend
cp .env.example .env
composer install && npm install
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

Configure `GITHUB_*` and `DASHBOARD_AUTH_*` per `.env.example`. Run `php artisan github:sync-org` and a queue worker when testing sync.

### Statamic CP (flat-file users)

Statamic Solo allows one CP user stored on disk (not the Laravel `users` table). Create or reset the super-admin after deploy:

```bash
php artisan statamic:make:user mjackson@thelyst.com --super --password="$SUPER_ADMIN_PASSWORD" --no-interaction
```

Site accounts (Breeze/Sanctum) are listed read-only at `/admin/site-users` (same BasicAuth as the ops dashboard).

Frontend: [`../frontend/`](../frontend/README.md).
