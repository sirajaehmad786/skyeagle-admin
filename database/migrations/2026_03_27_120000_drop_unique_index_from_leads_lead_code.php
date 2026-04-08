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
        if (! Schema::hasColumn('leads', 'lead_code')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            try {
                $table->dropUnique('leads_lead_code_unique');
            } catch (\Throwable $e) {
                // Ignore when index does not exist.
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('leads', 'lead_code')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->unique('lead_code');
        });
    }
};
