<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run if an older 2026_03_24 migration had already run before total_cwb was included in its up().
     */
    public function up(): void
    {
        if (Schema::hasColumn('quotation_hotels', 'total_cwb')) {
            Schema::table('quotation_hotels', function (Blueprint $table) {
                $table->dropColumn('total_cwb');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('quotation_hotels', 'total_cwb')) {
            Schema::table('quotation_hotels', function (Blueprint $table) {
                $table->integer('total_cwb')->default(0)->after('total_adult');
            });
        }
    }
};
