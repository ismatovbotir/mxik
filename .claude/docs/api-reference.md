# API Reference

Base URL: `http://localhost:8000/api` (dev)

## GET /api/class-codes

Returns paginated list of MXIK codes.

**Query params:**

| Param | Type | Default | Description |
|---|---|---|---|
| `per_page` | int | 100 | Records per page (max 1000) |
| `page` | int | 1 | Page number |
| `search` | string | — | Search by `id` (LIKE), `name` (LIKE), `gtin` (exact) |
| `status` | string | — | Filter by status: `1`, `2`, or `3` |

**Response:**
```json
{
  "data": [
    {
      "mxik": "03004141010063001",
      "new_mxik_code": null,
      "status": "1",
      "name": "Метформин",
      "description": null,
      "gtin": "4780201000123",
      "label": false,
      "use_package": true,
      "packages": [
        { "id": 1, "name": "штука", "package_type": "1" }
      ]
    }
  ],
  "links": { ... },
  "meta": { "current_page": 1, "total": 50000, ... }
}
```

## GET /api/class-codes/{mxik}

Returns a single MXIK record with its packages.

**Response:** same shape as one item in the list above.

**404:**
```json
{ "message": "Not found." }
```

## Status Codes

| Value | Meaning |
|---|---|
| `1` | Active |
| `2` | Changed (has `new_mxik_code`) |
| `3` | Default |

## Resource Shape (ClassCodeResource)

Fields returned: `mxik`, `new_mxik_code`, `status`, `name`, `description`, `gtin`, `label`, `use_package`, `packages[]`.

Note: `packages` is only included when the relation is loaded (always on `show`, always on `index`).
