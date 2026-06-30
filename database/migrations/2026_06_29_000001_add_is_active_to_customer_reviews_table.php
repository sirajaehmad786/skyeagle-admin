<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_reviews', 'is_active')) {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('sort_order')
                    ->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('customer_reviews', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
