<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. Amounts are now taken from quotation (flight, visa, hotel, sightseeing) directly.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'flight_price',
                'visa_price',
                'hotels_price',
                'sightseeing_price',
                'total',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->double('flight_price', 10, 2)->default(0)->after('user_id');
            $table->double('visa_price', 10, 2)->default(0)->after('flight_price');
            $table->double('hotels_price', 10, 2)->default(0)->after('visa_price');
            $table->double('sightseeing_price', 10, 2)->default(0)->after('hotels_price');
            $table->double('total', 10, 2)->default(0)->after('sightseeing_price');
        });
    }
};
