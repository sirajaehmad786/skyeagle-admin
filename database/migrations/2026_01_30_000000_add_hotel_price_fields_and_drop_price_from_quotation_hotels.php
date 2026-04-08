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
        Schema::table('quotation_hotels', function (Blueprint $table) {
            $table->integer('triple_room')->default(0)->after('total_room');
            $table->decimal('triple_room_price', 10, 2)->default(0)->after('triple_room');
            $table->decimal('total_room_price', 10, 2)->default(0)->after('triple_room_price');
            $table->decimal('total_cnb_price', 10, 2)->default(0)->after('total_cnb');
        });

        Schema::table('quotation_hotels', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_hotels', function (Blueprint $table) {
            $table->dropColumn(['triple_room', 'triple_room_price', 'total_room_price', 'total_cnb_price']);
        });

        Schema::table('quotation_hotels', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('total_room');
        });
    }
};
