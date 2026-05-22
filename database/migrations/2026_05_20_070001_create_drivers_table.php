<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('offline');
            $table->timestamps();

            $table->index('status');
        });

        // PostGIS geography point + GiST index. Created via raw DDL so the
        // exact type/SRID is explicit; the GiST index is what drives the
        // `<->` KNN ordering in the nearest-driver query.
        DB::statement('ALTER TABLE drivers ADD COLUMN location geography(Point, 4326)');
        DB::statement('CREATE INDEX drivers_location_gix ON drivers USING GIST (location)');
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
