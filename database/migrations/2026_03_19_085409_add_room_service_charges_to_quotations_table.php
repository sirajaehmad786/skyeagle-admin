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
            $table->decimal('sightseeing_adult_service_charge', 10, 2)
                ->default(0)
                ->after('total_cnb_service_price');

            $table->decimal('sightseeing_child_service_charge', 10, 2)
                ->default(0)
                ->after('sightseeing_adult_service_charge');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'sightseeing_adult_service_charge',
                'sightseeing_child_service_charge'
            ]);
        });
    }
};
