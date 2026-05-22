# Part 2 — Scaling writes without sacrificing reads

**Scenario:** millions of orders arrive in bursts (write pressure), while the dispatcher's
"active orders" screen — reading the same table — has crawled past 5 seconds (read pressure).

## Start by measuring, not by reaching for Redis

Before changing the architecture I'd confirm *where* the 5 seconds goes: `EXPLAIN` the active-orders
query, check for a full scan, and separate "slow query" from "table contended by the burst INSERTs."
The cheapest fix that resolves the symptom wins; the expensive ones are justified only once the cheap
ones prove insufficient. "I measured" versus "Redis because it's fast" is the difference between
fixing the bottleneck and adding a second system to maintain.

## (a) The right index — almost always first

The active-orders screen reads a tiny, bounded slice (`status = 'pending'`, recent) of a huge table.
On Postgres a **partial index** — `(created_at) WHERE status = 'pending'` — indexes only the hot rows,
so it stays small even as completed orders grow into the millions, turning a multi-million-row scan
into a tiny index range scan that brings 5s back under 100ms. **Cost:** every burst INSERT maintains
the index, a small, predictable write tax. It does nothing for raw INSERT throughput, but it directly
targets the read symptom and is reversible. **I start here.**

## (c) A separate "active orders" table — the structural fix

Reads and writes contend because they share one table. Keep only *open* orders in a small hot table
(rows move to a cold/archive table on completion). The dispatcher reads a table that stays small
regardless of historical volume; the archive absorbs the millions. On Postgres, **declarative
partitioning** (by status or time) gives the same hot/cold split natively, without app-managed row
moves. **Cost:** a state transition that moves rows (or partition routing), plus reporting now spans
partitions. This is the durable answer when the table's *size itself* is the problem — I'd reach for
it when indexing alone stops keeping up.

## (b) Redis cache — last, and surgically

Redis helps the *write* burst (buffer/queue incoming orders, flush in batches) or caches the
active-orders read. But it adds a second source of truth, cache-invalidation, and a new failure mode.
Note that before adding it, a Postgres **intake table drained by workers using `FOR UPDATE SKIP
LOCKED`** can already absorb the burst without a second system. I'd reserve Redis for when even that
ingest path saturates — rather than as a blanket "make reads fast" layer the partial index already
provides.

## Order of attack

Index (a) → hot/cold split (c) → Redis (b) for ingest buffering. Cheapest and most reversible first;
each step taken only when measurement shows the previous one is exhausted.
