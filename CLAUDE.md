# MXIK — Laravel API (Backend, Part 1)

## Project Purpose

Synchronises the MXIK classifier (Миллий классификатор товаров и услуг) from `tasnif.soliq.uz` every 2 hours and serves the data via REST API. Root route renders an analytics dashboard.

```
tasnif.soliq.uz → [every 2h: groups + history] → SQLite → REST API → Clients
                                                         ↓
                                          Dashboard (/) — stats, charts
```

## Stack

- **Framework:** Laravel 11
- **DB (dev):** SQLite
- **Queue:** database driver
- **Sync:** Laravel Scheduler (`everyTwoHours`) + `MxikSyncService`
- **Dashboard:** Tailwind CDN + Chart.js CDN (no build step)
- **JSON import:** `halaxa/json-machine` (streaming, for 400 MB+ files)

## Key Components

| Component | Path |
|---|---|
| Sync service | `app/Services/MxikSyncService.php` |
| Dashboard service | `app/Services/DashboardService.php` |
| Dashboard controller | `app/Http/Controllers/DashboardController.php` |
| Dashboard view | `resources/views/dashboard.blade.php` |
| API controller | `app/Http/Controllers/Api/ClassCodeController.php` |
| API resource | `app/Http/Resources/ClassCodeResource.php` |
| Artisan sync | `app/Console/Commands/MxikSyncCommand.php` |
| Artisan import | `app/Console/Commands/MxikImportCommand.php` |
| Scheduler | `routes/console.php` — `everyTwoHours()` |
| API routes | `routes/api.php` |

See `.claude/docs/` for detailed reference docs.

## Data Sources

### 1. Groups (one request, 117 records)
```
GET https://tasnif.soliq.uz/api/cl-api/integration-mxik/references/group/list
Fields: groupCode, nameUZ, nameRU, nameLAT
```

### 2. History (paginated, size=1000)
```
GET https://tasnif.soliq.uz/api/cl-api/integration-mxik/get/history/time
    ?page=0&size=1000&startDate={ms}&endDate={ms}
```
- `startDate`/`endDate` — Unix **milliseconds**
- Max window: **6 days**
- `startDate` = `last_sync_at` from `settings` (seconds in DB, ×1000 for API)
- First-run fallback: `now - 6 days`
- Pattern: probe `size=1` → `recordTotal` → `ceil(total/1000)` pages → loop
- After success: `last_sync_at = $endDate / 1000`

### 3. Bulk JSON import (`public/mxik.json`, gitignored)
- 400 MB+, streamed via `JsonMachine`
- Field names differ: `mxikNameUz`/`mxikNameRu`/`mxikNameLat` instead of `name`
- `createdAt`, `updateAt` — Unix milliseconds

### Date handling
| Location | Format |
|---|---|
| API params (`startDate`, `endDate`) | Unix ms (seconds × 1000) |
| DB (`settings.last_sync_at`) | Unix seconds |
| Conversion | `$ts * 1000` → API; `$endDate / 1000` → DB |

## Database Tables

**`class_groups`** — classifier groups
- `id` (unsignedInteger, PK) = `groupCode`
- `name_uz`, `name_ru`, `name_lat`

**`class_codes`** — MXIK product/service codes (17-digit string PK)
- `new_mxik_code` (nullable), `status` ('1'=Active, '2'=Changed, '3'=Default)
- `class_group_id` — FK, derived as `substr(mxik, 0, 3)`
- `name`, `gtin` (nullable = `internationalCode`)
- `label`, `use_package` (boolean, snake_case in model/fillable)
- `labelForCheck`, `usePackage`, `cashSale` — camelCase column names in upsert

**`package_codes`** — units of measure
- `id` (unsignedBigInteger, PK) = `code`
- `class_code_id` (FK → `class_codes.id`), `name` = `nameUz`, `package_type`

**`settings`** — key/value config (`last_sync_at` = Unix seconds)

**`gtin_prefixes`** — country lookup by GTIN prefix (1–3 digit string PK)
- `country`, `country_code`, `flag`

## API Endpoints

```
GET /api/class-codes                    — paginated list (default 100, max 1000 per_page)
GET /api/class-codes?search={query}     — search by id, name, or exact gtin
GET /api/class-codes?status={status}    — filter by status
GET /api/class-codes/{mxik}             — single record with packages
```

All responses JSON. 404 → `{"message": "Not found."}`.

## Dashboard (/)

- KPI cards: Total, GTIN Coverage %, Active, Changed, Added This Month, Countries
- Charts: Monthly growth trend (12m line), Status doughnut, Top groups horizontal bar, Year-over-year bar
- Compliance flags: label, labelForCheck, usePackage, cashSale — counts + % of total
- Country table: top 10 by GTIN prefix (LEFT JOIN `gtin_prefixes`, 3→2→1 digit fallback)
- Last added / last updated item cards with product image (see below)
- GTIN/code live search in header — calls `/api/class-codes?search=` with 300ms debounce
- Stats: `Cache::rememberForever('dashboard_stats', ...)` — cleared after each sync

## Product Images

Every `class_codes` record has a product image hosted on tasnif.soliq.uz:

```
https://tasnif.soliq.uz/api/cls-api/integration-mxik/references/get/file/{id}_1.png
```

- `{id}` = the 17-digit MXIK code (e.g. `03105001009000000`)
- Always use `onerror` fallback — many codes have no image (404)
- Pattern applies anywhere a `class_code` is displayed: dashboard cards, detail pages, search results, etc.

## Dev Commands

```bash
php artisan serve           # local server
php artisan migrate         # run migrations
php artisan migrate:fresh   # recreate DB
php artisan queue:work      # queue worker
php artisan schedule:work   # scheduler
php artisan mxik:sync       # manual sync from API
php artisan mxik:import     # import from public/mxik.json
```

## Conventions

- Upsert by `id`, never delete records
- `Setting::get('key')` / `Setting::set('key', $val)`
- `Log::error()` for sync failures
- `last_sync_at` updated only on success, value = `$endDate / 1000`
- `labelForCheck`, `usePackage`, `cashSale` use camelCase column names in upsert (not snake_case)
