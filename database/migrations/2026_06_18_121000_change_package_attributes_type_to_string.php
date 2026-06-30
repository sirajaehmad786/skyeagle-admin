<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('package_attributes') || ! Schema::hasColumn('package_attributes', 'type')) {
            return;
        }

        DB::statement('ALTER TABLE package_attributes MODIFY type VARCHAR(100) NOT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('package_attributes') || ! Schema::hasColumn('package_attributes', 'type')) {
            return;
        }

        DB::statement("ALTER TABLE package_attributes MODIFY type ENUM('popular', 'accommodation', 'activity', 'meal_plan') NOT NULL");
    }
};
