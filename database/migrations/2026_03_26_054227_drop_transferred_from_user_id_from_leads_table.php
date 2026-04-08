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
        if (! Schema::hasColumn('leads', 'transferred_from_user_id')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            try {
                $table->dropForeign(['transferred_from_user_id']);
            } catch (\Throwable $e) {
                // Ignore if the foreign key does not exist.
            }

            $table->dropColumn('transferred_from_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('leads', 'transferred_from_user_id')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('transferred_from_user_id')->nullable();
            $table->foreign('transferred_from_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};
