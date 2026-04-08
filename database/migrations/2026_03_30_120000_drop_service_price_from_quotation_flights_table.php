<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_flights', function (Blueprint $table) {
            $table->dropColumn('service_price');
        });
    }

    public function down(): void
    {
        Schema::table('quotation_flights', function (Blueprint $table) {
            $table->decimal('service_price', 10, 2)
                ->default(0)
                ->after('infant_price');
        });
    }
};

