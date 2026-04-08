<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('quotations', 'sightseeing_service_price')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->decimal('sightseeing_service_price', 10, 2)
                    ->default(0)
                    ->after('hotels_service_price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('quotations', 'sightseeing_service_price')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->dropColumn('sightseeing_service_price');
            });
        }
    }
};
