<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'single_room_service_price')) {
                $table->decimal('single_room_service_price', 10, 2)
                    ->default(0)
                    ->after('total_cnb_service_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'single_room_service_price')) {
                $table->dropColumn('single_room_service_price');
            }
        });
    }
};

