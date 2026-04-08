<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('hotel_requirements', 50)->nullable()->after('flight_requirements');
            $table->string('sightseeing_requirements', 50)->nullable()->after('hotel_requirements');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['hotel_requirements', 'sightseeing_requirements']);
        });
    }
};
