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
            $table->string('visa_type')->nullable()->after('visa_infant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_visas', function (Blueprint $table) {
            $table->dropColumn('visa_type');
        });
    }
};
