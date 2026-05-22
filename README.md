# WINCH — Order Assignment

Assign transport orders to the nearest available driver. Laravel 13 API + a single Vue 3 dispatcher
screen, organised as a strict Domain-Driven design.

> **This repo covers Part 1 (build).** The Part 2 written architecture decision — scaling writes
> without sacrificing reads — is in **[docs/architecture-decision.md](docs/architecture-decision.md)**.

## Stack & why

- **Laravel 13** (PHP 8.3+) and **Vue 3 / Vite** — required by the brief; Vue is mounted on one Blade
  page (no Inertia/Pinia — overkill for a single screen).
- **PostgreSQL** over MySQL — this is a dispatch workload, and Postgres fits it better:
  - `SELECT … FOR UPDATE SKIP LOCKED` for queue-style assignment without lock contention,
  - **partial indexes** (e.g. only `pending` orders) to keep the hot read path tiny as the table grows,
  - native **declarative partitioning** for the write/archive scaling path.
- **PostGIS** — the nearest-driver lookup *is* the core query. A `geography(Point,4326)` column + GiST
  index + the `<->` KNN operator gives genuine **index-driven** nearest-neighbour
  (`ORDER BY location <-> ST_MakePoint(?,?) LIMIT n`) — logarithmic, not a scan-and-sort. PostGIS is
  far more capable than MySQL's spatial support; when proximity is the product, that capability
  justifies the database choice itself. The trade-off — PostGIS is a hard dependency and geography
  columns are more awkward to dump/restore — is worth it here.
- **Sanctum** — token auth for the API (no login UI; see *Auth*).

## Architecture (DDD)

```
src/
├── Domain/
│   ├── Order/      # OrderContract + OrderService, OrderSnapshot DTO, OrderStatus, ActiveScope
│   ├── Driver/     # DriverContract + DriverService (PostGIS KNN), AvailableDriver DTO, OnlineScope
│   └── Dispatch/   # AssignOrderToDriverAction, OrderAssignmentContract, AssignmentResult
└── Presentation/
    └── Api/        # Controllers, Requests, Resources, Routes/api.php
```

- Controllers hold no business logic — they call a domain **Contract** and return a Resource.
- Every Service sits behind a Contract, bound via DI in each domain's `ServiceProvider`.
- Domains cross **only** through Contracts that return DTOs — never each other's Eloquent models.
- Domain exceptions → HTTP status codes in `bootstrap/app.php`, keeping the domain HTTP-free.
- Empty mandated folders (`Abilities`, `Traits`, `Observers`) are intentional — no need yet.

## Run

Requires PostgreSQL **with PostGIS**, and PHP's `pdo_pgsql` extension.

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate     # set DB_* if needed (default db: winch)
createdb winch                                        # the migration runs CREATE EXTENSION postgis
php artisan migrate --seed                            # seeds drivers + pending orders
php artisan serve            # http://127.0.0.1:8000
npm run dev                  # Vite, separate terminal
```

Open `/` — the dispatcher screen lists pending orders; **Assign** picks the nearest available driver.

## API

Under `/api`, behind `auth:sanctum`.

| Method | Path | Purpose |
| --- | --- | --- |
| `POST` | `/api/orders/{id}/assign` | Assign the order to the nearest available driver |
| `GET`  | `/api/orders?status=&per_page=` | List orders (filter by status, paginated) |
| `GET`  | `/api/drivers/{id}/orders?status=&per_page=` | A driver's orders (filter, paginated) |

`assign` returns `409` if the order isn't pending, `404` if missing, `422` if no driver is available.

## Key decisions

- **Concurrency** — assignment runs in one transaction: lock the order row (idempotent retry, no
  double-assign), then lock the chosen driver row and re-check before binding (no two orders take the
  same driver).
- **Read path at scale** — a partial index on pending orders keeps the active-orders screen fast as
  the table grows. Full read/write scaling discussion in [docs/architecture-decision.md](docs/architecture-decision.md).

## Auth

Token-based (Sanctum); no login UI by design. The `/` route mints a demo token for the SPA. For
curl/Postman: `php artisan tinker` → `User::first()->createToken('cli')->plainTextToken`.

## Known gaps (by design)

- No login UI (token stubbed); no real-time push (manual refresh).
- No automated tests shipped — the assignment rules are unit-testable via `OrderAssignmentContract`
  against a seeded DB; a feature-test harness is the first follow-up.
- PostGIS is a hard dependency (the deliberate cost of index-driven proximity search).

What we'd do with more time → **[docs/if-we-had-time.md](docs/if-we-had-time.md)**.
