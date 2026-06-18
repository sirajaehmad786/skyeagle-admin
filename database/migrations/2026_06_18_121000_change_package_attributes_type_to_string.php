<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE package_attributes MODIFY type VARCHAR(100) NOT NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE package_attributes MODIFY type ENUM('popular', 'accommodation', 'activity', 'meal_plan') NOT NULL");
    }
};
