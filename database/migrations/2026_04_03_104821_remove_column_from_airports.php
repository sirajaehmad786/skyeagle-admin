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
        Schema::table('airports', function (Blueprint $table) {
            if(Schema::hasColumn('airports', 'state_UT')) {
                $table->dropColumn('state_UT');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('airports', function (Blueprint $table) {
            if(!Schema::hasColumn('airports', 'state_UT')) {
                $table->string('state_ut')->nullable();
            }
        });
    }
};
