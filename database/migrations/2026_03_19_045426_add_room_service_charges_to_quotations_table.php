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
            $table->decimal('double_room_service_price', 10, 2)
                ->default(0)
                ->after('visa_service_price');

            $table->decimal('triple_room_service_price', 10, 2)
                ->default(0)
                ->after('double_room_service_price');

            $table->decimal('total_cnb_service_price', 10, 2)
                ->default(0)
                ->after('triple_room_service_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'double_room_service_price',
                'triple_room_service_price',
                'total_cnb_service_price'
            ]);
        });
    }
};
