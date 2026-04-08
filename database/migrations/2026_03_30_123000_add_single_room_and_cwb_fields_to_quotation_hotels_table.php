<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('quotation_hotels', 'single_room')) {
                $table->integer('single_room')->default(0)->after('total_room');
            }

            if (!Schema::hasColumn('quotation_hotels', 'single_room_price')) {
                $table->decimal('single_room_price', 10, 2)->default(0)->after('single_room');
            }

            // These columns were removed in older migrations; re-add if missing.
            if (!Schema::hasColumn('quotation_hotels', 'total_cwb')) {
                $table->integer('total_cwb')->default(0)->after('total_adult');
            }

            if (!Schema::hasColumn('quotation_hotels', 'total_cwb_price')) {
                $table->decimal('total_cwb_price', 10, 2)->default(0)->after('total_cwb');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotation_hotels', function (Blueprint $table) {
            if (Schema::hasColumn('quotation_hotels', 'single_room')) {
                $table->dropColumn('single_room');
            }
            if (Schema::hasColumn('quotation_hotels', 'single_room_price')) {
                $table->dropColumn('single_room_price');
            }

            if (Schema::hasColumn('quotation_hotels', 'total_cwb_price')) {
                $table->dropColumn('total_cwb_price');
            }
            if (Schema::hasColumn('quotation_hotels', 'total_cwb')) {
                $table->dropColumn('total_cwb');
            }
        });
    }
};

