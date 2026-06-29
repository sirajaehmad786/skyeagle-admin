<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'destination_id')) {
                $table->foreignId('destination_id')
                    ->nullable()
                    ->after('destination_city')
                    ->constrained('destinations')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'destination_id')) {
                $table->dropConstrainedForeignId('destination_id');
            }
        });
    }
};
