<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // delete column
            $table->dropColumn('is_active');

            // add new column
            $table->boolean('is_transfer')->default(0)->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // rollback ke liye wapas add
            $table->boolean('is_active')->default(1);

            // new column remove
            $table->dropColumn('is_transfer');
        });
    }
};