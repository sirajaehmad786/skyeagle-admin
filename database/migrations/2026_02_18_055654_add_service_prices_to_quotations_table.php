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
            $table->decimal('visa_service_price', 10, 2)
                ->default(0)
                ->after('amount_description_services');

            $table->decimal('hotels_service_price', 10, 2)
                ->default(0)
                ->after('visa_service_price');

            $table->decimal('sightseeing_service_price', 10, 2)
                ->default(0)
                ->after('hotels_service_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'visa_service_price',
                'hotels_service_price',
                'sightseeing_service_price'
            ]);
        });
    }
};
