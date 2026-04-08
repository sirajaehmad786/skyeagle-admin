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
        Schema::table('quotation_flights', function (Blueprint $table) {
            $table->decimal('adult_price', 10, 2)->default(0)->after('flight_infant');
            $table->decimal('child_price', 10, 2)->default(0)->after('adult_price');
            $table->decimal('infant_price', 10, 2)->default(0)->after('child_price');
            $table->dropColumn('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_flights', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('flight_infant');
            $table->dropColumn(['adult_price', 'child_price', 'infant_price']);
        });
    }
};
