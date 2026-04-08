<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $path = database_path('sql/world.sql');
        if (!file_exists($path)) {
            throw new \Exception("SQL file not found at path: {$path}");
        }

        $sql = file_get_contents($path);

        // Execute the SQL file
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared(<<<'SQL'
        DROP TABLE IF EXISTS `cities`;
        DROP TABLE IF EXISTS `states`;
        DROP TABLE IF EXISTS `countries`;
        DROP TABLE IF EXISTS `subregions`;
        DROP TABLE IF EXISTS `regions`;
        SQL);
    }
};
