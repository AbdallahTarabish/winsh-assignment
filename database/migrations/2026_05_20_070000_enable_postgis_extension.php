<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Nearest-driver search relies on PostGIS (geography + GiST KNN).
        // Requires the postgis extension to be available on the server.
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');
    }

    public function down(): void
    {
        DB::statement('DROP EXTENSION IF EXISTS postgis');
    }
};
