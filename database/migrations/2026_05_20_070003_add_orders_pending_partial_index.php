<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The dispatcher's "active orders" screen reads only pending orders,
        // newest first. A PARTIAL index indexes just those rows (pre-sorted by
        // created_at), so it stays tiny even when the table holds millions of
        // completed/cancelled orders — the screen's query never touches them.
        // This is the cheapest fix for the read-pressure scenario; see
        // docs/architecture-decision.md.
        DB::statement(
            "CREATE INDEX orders_pending_created_at_idx ON orders (created_at DESC) WHERE status = 'pending'"
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS orders_pending_created_at_idx');
    }
};
