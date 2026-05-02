# Models Reference

## ClassCode

File: `app/Models/ClassCode.php`

- PK: `id` (string, `$incrementing = false`, `$keyType = 'string'`)
- Fillable: `id`, `new_mxik_code`, `status`, `class_group_id`, `name`, `description`, `gtin`, `label`, `use_package`, `only_card`
- Casts: `label` → boolean, `use_package` → boolean, `only_card` → boolean
- Relations: `packageCodes()` hasMany `PackageCode`, `classGroup()` belongsTo `ClassGroup`

**Warning:** upsert in `MxikSyncService` writes camelCase columns (`labelForCheck`, `usePackage`, `cashSale`) that are NOT in `$fillable` — they bypass the model and go straight to the DB via `upsert()`.

## PackageCode

File: `app/Models/PackageCode.php`

- PK: `id` (unsignedBigInteger)
- Belongs to `ClassCode` via `class_code_id`
- Fields: `name`, `package_type`

## ClassGroup

File: `app/Models/ClassGroup.php`

- PK: `id` (unsignedInteger) = `groupCode` from API
- Fields: `name_uz`, `name_ru`, `name_lat`
- Has many `ClassCode` via `class_group_id`

## Setting

File: `app/Models/Setting.php`

- Key/value store, unique `key` column
- `Setting::get(string $key, mixed $default = null): mixed`
- `Setting::set(string $key, mixed $value): void`
- Important key: `last_sync_at` — Unix seconds of last successful history sync

## GtinPrefix

File: `app/Models/GtinPrefix.php`

- PK: `prefix` (string, 1–3 digits)
- Fields: `country`, `country_code`, `flag` (emoji)
- Used in `DashboardService` via raw LEFT JOIN (3-digit → 2-digit → 1-digit fallback)
