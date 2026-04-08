<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('sightseeing_adult_price', 10, 2)->nullable()->after('exclusion');
            $table->decimal('sightseeing_child_price', 10, 2)->nullable()->after('sightseeing_adult_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['sightseeing_adult_price', 'sightseeing_child_price']);
        });
    }
};
