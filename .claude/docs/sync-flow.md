# Sync Flow — MxikSyncService

## Entry Points

| Trigger | Path |
|---|---|
| Scheduler (every 2h) | `routes/console.php` → `MxikSyncCommand` |
| Manual | `php artisan mxik:sync` |
| Bulk import | `php artisan mxik:import` (from `public/mxik.json`) |

## `sync()` — API Sync Flow

```
1. syncGroups()
   └── GET /references/group/list
   └── ClassGroup::upsert() by id

2. Resolve date window
   ├── last_sync_at from settings (Unix seconds)
   ├── startTimestamp = lastSync ?? now()-6days
   ├── startDate = startTimestamp * 1000   (ms for API)
   └── endDate   = (startTimestamp + 6*86400) * 1000

3. Probe: GET history?page=0&size=1&startDate&endDate
   └── read recordTotal

4. Loop pages 0..ceil(total/1000)-1
   └── GET history?page=N&size=1000
   └── saveItems(data)
       ├── ClassCode::upsert()
       └── PackageCode::upsert()

5. On success: Setting::set('last_sync_at', endDate/1000)
6. MxikSyncCommand clears 'dashboard_stats' cache
```

## `importFromFile()` — Bulk Import Flow

```
JsonMachine::fromFile(path)   ← streaming, handles 400 MB+
  └── batch 500 items
      └── saveFileItems(batch)
          ├── ClassCode::upsert()
          └── PackageCode::upsert()

After import: last_sync_at = max(created_at) of class_codes
```

## Field Mapping

| API field | DB column | Notes |
|---|---|---|
| `mxik` | `class_codes.id` | 17-digit string PK |
| `newMxikCOde` | `new_mxik_code` | typo in API preserved |
| `internationalCode` | `gtin` | nullable |
| `name` | `name` | history API |
| `mxikNameUz` | `name` | file import |
| `labelForCheck` | `labelForCheck` | camelCase column |
| `usePackage` | `usePackage` | camelCase column |
| `cashSale` | `cashSale` | hardcoded `true` |
| `packages[].code` | `package_codes.id` | |
| `packages[].nameUz` | `package_codes.name` | |
| `packages[].packageType` | `package_codes.package_type` | |

## Important Notes

- `newMxikCOde` — the API has a typo (capital C), used as-is
- `class_group_id` is derived locally: `(int) substr($mxik, 0, 3)`
- Dates `createdAt`/`updateAt` in API are Unix ms → stored as Carbon after ÷1000
- `updateAt` (not `updatedAt`) — typo in API, used as-is
