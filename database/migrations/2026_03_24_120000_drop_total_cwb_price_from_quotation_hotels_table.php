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
        $columns = [];
        if (Schema::hasColumn('quotation_hotels', 'total_cwb_price')) {
            $columns[] = 'total_cwb_price';
        }
        if (Schema::hasColumn('quotation_hotels', 'total_cwb')) {
            $columns[] = 'total_cwb';
        }

        if ($columns !== []) {
            Schema::table('quotation_hotels', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_hotels', function (Blueprint $table) {
            $table->integer('total_cwb')->default(0)->after('total_adult');
            $table->decimal('total_cwb_price', 10, 2)->default(0)->after('total_cwb');
        });
    }
};
