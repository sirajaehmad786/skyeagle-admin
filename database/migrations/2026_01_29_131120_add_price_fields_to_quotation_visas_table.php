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
        Schema::table('quotation_visas', function (Blueprint $table) {
            $table->decimal('visa_adult_price', 10, 2)->nullable()->after('visa_infant');
            $table->decimal('visa_child_price', 10, 2)->nullable()->after('visa_adult_price');
            $table->dropColumn('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_visas', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('visa_infant');
            $table->dropColumn(['visa_adult_price', 'visa_child_price']);
        });
    }
};
