<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_flights', function (Blueprint $table) {
            $table->decimal('service_price_adult', 10, 2)->default(0)->after('adult_price');
            $table->decimal('service_price_child', 10, 2)->default(0)->after('child_price');
            $table->decimal('service_price_infant', 10, 2)->default(0)->after('infant_price');
        });
    }

    public function down(): void
    {
        Schema::table('quotation_flights', function (Blueprint $table) {
            $table->dropColumn(['service_price_adult', 'service_price_child', 'service_price_infant']);
        });
    }
};

