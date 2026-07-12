# AI Context — Ngaji Nusa

Single-file onboarding doc for any AI assistant (Claude, ChatGPT, Gemini, Copilot, Cursor, etc.) working in this repo. Read this first, before `CLAUDE.md` or the other `docs/*.md` files — it supersedes them where they conflict (see §9). Keep it updated whenever architecture, conventions, or features change.

## 1. What this project is

Ngaji Nusa is a registration platform for online Islamic study sessions (*pengajian*) held via Zoom, open to the general public across all ages.

- **Murid** = student registering for online ngaji (not an employee/payroll concept).
- Payment is semi-subscription: billed monthly, **manual** — not auto-charged.
- Solo full-stack developer, no other contributors. Treat the human as a close, senior peer, not a client.

## 2. Stack

| Layer | Choice |
|---|---|
| Framework | Laravel 13 |
| Language | PHP 8.5 (composer.json currently pins `^8.3`) |
| Database | MariaDB in production; **SQLite** locally (`database/database.sqlite`) |
| Frontend | Blade + Bootstrap 5 + vanilla JS — no Livewire, Vue, or React |
| Build tool | Vite + Tailwind 4 devDependency present, but Bootstrap 5 is the actual UI system in views |
| Server | Ubuntu + Nginx + PHP-FPM |
| Auth | **Implemented**, session-based, via Laravel's built-in `Auth` facade — not Breeze (Breeze still isn't in `composer.json`). `AuthController` + `LoginRequest` handle `GET/POST /login` and `POST /logout`, rate-limited 5 attempts/min per email+IP. No role/permission checks wired up yet. |
| Payment | **Not yet implemented.** Midtrans is referenced only as static text in `resources/views/admin/pengaturan.blade.php`, no SDK, no config, no controller. |
| App sits behind Cloudflare | `bootstrap/app.php` configures `trustProxies` with Cloudflare's IP ranges |

## 3. Architecture

```
Route → Controller → Service → Repository (only if already used) → Model
```

- **Controller**: validate via Form Request, call Service, return response. Stays thin.
- **Service**: business logic, transactions, reusable. Business logic belongs here.
- **Model**: persistence only, no business logic.
- **No Repository pattern** — deliberate decision (`docs/decisions.md`).

**⚠️ Reality gap — read before writing code:** `app/Services` does not exist yet. All logic (e.g. WhatsApp number normalization) currently lives directly in `MuridController`. Treat the Service layer as the target for *new, non-trivial* logic. Do not refactor existing controller logic into a Service speculatively — ask first if unsure.

Validation always goes through Form Request classes (`app/Http/Requests`), never inline in the controller.

## 4. Folder structure (actual, not aspirational)

```
app/
  Http/Controllers/    Controller.php (base), MuridController.php, AuthController.php
  Http/Middleware/      LogVisitor.php
  Http/Requests/        StoreMuridRequest.php, StorePostRequest.php (orphaned — see §8), LoginRequest.php
  Models/                AdminSetting.php, Murid.php, User.php, VisitorLog.php, ReferralAgent.php
  Services/              ReferralAgentService.php
  Providers/
database/
  migrations/            users/cache/jobs (Laravel defaults), visitor_logs, murid, admin_settings, referral_agents, murid.referral_agent_id
  factories/, seeders/    AdminUserSeeder.php (not yet called from DatabaseSeeder::run() — see §8)
resources/views/
  admin/                 dashboard, murid, guru, jadwal, paket, transaksi, laporan, pengaturan, partials/
  auth/login.blade.php
  layouts/               app.blade.php, admin.blade.php, auth.blade.php
  pages/home.blade.php
  welcome.blade.php     (Laravel default, unused)
routes/
  web.php, console.php
tests/
  Feature/ExampleTest.php, Unit/ExampleTest.php (both still the Laravel scaffolding defaults — no real tests yet)
docs/                   this file + legacy per-topic docs (see §9)
CLAUDE.md               project-wide instructions for AI assistants — do not edit without being asked
PROJECT_MEMORY.md       working notes from a prior codebase audit — do not overwrite
```

`app/Services` now exists (`ReferralAgentService`). `app/Repositories` and `app/Policies` still don't.

## 5. Coding conventions

- PSR-12. Strict typing where practical. Functions under ~50 lines. Prefer early return over nested ifs. Prefer dependency injection over static methods (existing `AdminSetting::get()` static helper is a pragmatic exception, not a pattern to expand). No unused imports, no duplicated code.
- Eloquent only, no raw SQL, no `SELECT *`. Eager-load to prevent N+1 (`with()`, `load()`, `paginate()`).
- Domain terms stay in **Indonesian** — `Murid`, `paket`, `level_belajar`, etc. Do not translate to English in code, DB, or UI.

| Type | Convention | Example |
|---|---|---|
| Controller | `PascalCase` + `Controller` | `MuridController` |
| Service | `PascalCase` + `Service` | `UserService` |
| Form Request | `Store/Update` + Model + `Request` | `StoreMuridRequest` |
| Model | PascalCase singular | `Murid`, `AdminSetting` |
| Migration | snake_case, descriptive | `create_murid_table` |
| Method | camelCase | `normalizeWhatsapp()` |
| DB columns/tables | snake_case | `wa_admin_number` |
| Route | kebab-case | `/daftar` |

## 6. Data model (current migrations)

- **`users`** — Laravel default auth table (name, email, password) + standard `password_reset_tokens`/`sessions`.
- **`murid`** — `nama`, `email`, `whatsapp` (stored as `62xxxxxxxxxx`), `level_belajar`, `paket`, `status` (default `Daftar`). Indexed on `status`, `email`.
  - `Murid::LEVEL_OPTIONS` = Hijaiyah, Iqra, Tahsin, Tajwid, Hafalan.
  - `Murid::PAKET_OPTIONS` = Basic, Pro, Premium, Platinum.
  - `Murid::STATUS_DAFTAR` = `'Daftar'` is the only defined status constant so far; migration comment notes more statuses (Aktif, Pending, Nonaktif, etc.) are coming in a later phase — **do not invent status values**, check with the owner.
- **`admin_settings`** — generic key/value store, e.g. `wa_admin_number`. Accessed via `AdminSetting::get($key, $default)`.
- **`visitor_logs`** — `visit_date` + `path` + `ip_address` unique triplet, `hit_count` incremented per hit. Written by `LogVisitor` middleware.
- **`referral_agents`** — `nama`, `whatsapp`, `kode` (unique), `status` (`Aktif`/`Nonaktif`, default `Aktif`, indexed). `murid.referral_agent_id` — nullable FK to `referral_agents`, `nullOnDelete()`.

## 7. Business rules (confirmed)

- Public registration flow: `POST /daftar` → `StoreMuridRequest` (validates + Indonesian error messages, forces JSON error responses even on non-AJAX-detected requests) → `MuridController::store()`.
- WhatsApp numbers are normalized to `62xxxxxxxxxx` (no `+`, no leading `0`) before storage, for consistent `wa.me` links.
- New registrations always get `status = Murid::STATUS_DAFTAR`.
- `/daftar` is throttled to 10 requests/minute/IP to prevent spam.
- `LogVisitor` middleware logs every non-admin `GET` request (dedup by date+path+IP, incrementing `hit_count`); purpose beyond basic analytics is undocumented.
- Payment is **manual monthly**, not auto-charged — never build auto-billing logic without explicit confirmation.
- Referral: `GET /daftar?ref=KODE` validates the code against an active `ReferralAgent` and queues it into a 30-day `referral_code` cookie (`ReferralAgentService::captureFromRequest`). `POST /daftar` resolves `referral_agent_id` from that cookie (`resolveAgentIdFromCookie`) — never entered manually by the user.
- Login: `POST /login` (`LoginRequest::authenticate`) is rate-limited to 5 attempts/min per email+IP; on success the session is regenerated and the user is redirected to `admin.dashboard`. `POST /logout` invalidates the session and regenerates the CSRF token.

## 8. Known inconsistencies / open items

- `StorePostRequest` exists in `app/Http/Requests` but no controller uses it, and `authorize()` returns `false`. Status unclear — don't delete or build around it without asking.
- `routes/web.php` admin routes (dashboard, transaksi, laporan, pengaturan, murid, guru, jadwal, paket) currently just return static Blade views inline — no controllers, no auth/role middleware wired up yet, despite the file's own comment saying they should be.
- `routes/web.php` registers `GET /login` **twice** — an old inline closure and the new `AuthController::create()`, both named `login`. The old closure (registered first) wins route matching, so `AuthController::create()` is currently dead code for GET requests. Needs cleanup (remove the old closure) — flagged, not yet fixed.
- `database/seeders/AdminUserSeeder.php` exists but isn't called from `DatabaseSeeder::run()` — won't seed via `php artisan db:seed` until wired in.
- Role/permission approach (Spatie vs custom) is undecided — don't implement either speculatively.
- `docs/features.md` lists a broad, unstatused feature wishlist (Authorization, Audit Log, Export/Import Excel, etc.) that does not reflect current implementation — treat §10 below (mirroring `docs/current_state.md`) as the source of truth for what's actually built.
- Zoom access delivery mechanism after payment: not designed yet.
- `paket` pricing/tier definitions beyond the four names in `Murid::PAKET_OPTIONS`: not documented anywhere.

## 9. Documentation map

- `CLAUDE.md` — instruction set AI assistants must follow (architecture rules, output rules, guardrails). Do not edit without being asked.
- `PROJECT_MEMORY.md` — a prior AI-generated audit of this codebase; overlaps heavily with this file. Do not overwrite.
- `docs/*.md` (architecture, conventions, coding_style, database, decisions, deployment, features, current_state, changelog, ai_workflow, todo) — original short per-topic docs. Several are aspirational or stale (see §8). This file (`ai-context.md`) is the consolidated, verified successor — when they conflict, trust this file and flag the discrepancy to the owner rather than silently picking one.

## 10. Feature status

**Done:** Login (real session auth, rate-limited), basic Dashboard view, User CRUD (model level only — no controller/UI seen), Murid registration (public form → DB), Referral Agent backend (model, migration, service, cookie capture — no admin UI yet, see `docs/todo.md`).
**In progress:** Member/Murid admin module (view scaffolded, no controller), Payment module (not started in code).
**Not started:** Reporting, Notifications, Role/permission system.

## 11. External integrations

None are actually wired up yet:

- **Midtrans** (payment) — planned per `docs/decisions.md`, mentioned only as placeholder text in a view. No SDK installed.
- **Laravel Breeze** — not used. Auth is hand-rolled with Laravel's built-in `Auth` facade (`AuthController` + `LoginRequest`); Breeze is still not installed.
- Default Laravel service stubs only (`config/services.php`): Postmark, Resend, SES, Slack notifications — none configured with real credentials in `.env.example`.
- Mail driver is `log` (dev), queue/cache/session all use `database` driver locally.

## 12. Files to not modify without explicit permission

- `CLAUDE.md`, `PROJECT_MEMORY.md`, existing `docs/*.md` files (add/update, don't overwrite silently).
- Any migration for `murid`, `admin_settings`, `visitor_logs`, `users` — no schema changes unless explicitly requested.
- Method names, route names, model names — no renaming without permission.
- `app/Http/Requests/StorePostRequest.php` — leave as-is until its purpose is clarified.

## 13. Common development commands

```bash
# Install & first-time setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build

# Local dev (server + queue worker + log tail + vite, all at once)
composer run dev

# Tests
composer run test
php artisan test

# Code style (Pint is installed as a dev dependency)
vendor/bin/pint

# Production-ish local commands
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 14. When unsure

Ask before making architectural changes. Don't guess project conventions — check this file, `CLAUDE.md`, and the actual code (not just the aspirational `docs/*.md` files) before assuming a pattern exists.

---
*Last verified against the codebase: 2026-07-12. Update this file whenever architecture, folder structure, business rules, or integrations change.*
