# If we had more time

Honest, prioritised list of what was consciously left out and what we'd do next.

## 1. Tests (the biggest gap)
- Feature tests for the assignment rules against a PostGIS test database: nearest selection,
  offline/busy exclusion, no-driver → `422`, idempotent retry, not-pending → `409`.
- A real concurrency test (two parallel connections racing for the same driver) to prove the
  `FOR UPDATE` locking, which a single-connection harness can't exercise.
- `docker-compose.yml` with `postgis/postgis` + a CI workflow, so "runs from zero" is guaranteed.

## 2. Real-time dispatch screen
Replace the manual Refresh with live updates (Laravel Reverb / Echo + WebSockets) so new pending
orders and assignments appear instantly — closer to an actual dispatch product.

## 3. Real auth & roles
A proper login (Sanctum SPA cookie auth) instead of the demo token injection, plus
authorization policies — this is what the empty `Models/Abilities` folder is reserved for.

## 4. A robust assignment lifecycle
- Driver **offer → accept / timeout → reassign** flow instead of an immediate hard bind.
- Denormalise driver availability (`current_order_id` flag maintained by an `Observer`) to drop the
  busy anti-join entirely — what the empty `Observers` folder is reserved for.
- A configurable max dispatch radius and an "order assigned" domain event for other contexts.

## 5. Write-path scaling (when load demands it)
The path from [architecture-decision.md](architecture-decision.md): batch inserts → decouple ingest
into an intake table drained by `SKIP LOCKED` workers → partition `orders` by time. Deliberately not
built now (no measured bottleneck), but this is the order we'd implement it.

## 6. Observability
Structured logging plus metrics on assignment latency, no-driver rate, and slow queries — so scaling
decisions stay measurement-driven rather than guessed.

## 7. API & geo polish
Cursor pagination for driver order history, rate limiting, an OpenAPI/Postman collection, a
driver heartbeat (`last_seen`) to define "online" more robustly, and a map view on the frontend.
