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
        Schema::table('customer_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'reviewer_email',
                'reviewer_phone',
                'reviewer_designation',
                'reviewer_company',
                'slug',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_reviews', function (Blueprint $table) {
            $table->string('reviewer_email')->nullable();
            $table->string('reviewer_phone')->nullable();
            $table->string('reviewer_designation')->nullable();
            $table->string('reviewer_company')->nullable();
            $table->string('slug')->nullable();
        });
    }
};
