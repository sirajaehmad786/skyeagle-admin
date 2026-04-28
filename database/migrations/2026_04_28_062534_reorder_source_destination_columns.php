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
        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'source_city')) {
                $table->dropColumn('source_city');
            }

            if (Schema::hasColumn('packages', 'destination_city')) {
                $table->dropColumn('destination_city');
            }
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->string('source_city')->after('short_title');
            $table->string('destination_city')->after('source_city');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            //
        });
    }
};
