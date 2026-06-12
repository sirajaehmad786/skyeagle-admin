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
        $columns = [
            'reviewer_email',
            'reviewer_phone',
            'reviewer_designation',
            'reviewer_company',
            'slug',
        ];

        $existing = array_filter($columns, fn ($column) => Schema::hasColumn('customer_reviews', $column));

        if (empty($existing)) {
            return;
        }

        Schema::table('customer_reviews', function (Blueprint $table) use ($existing) {
            $table->dropColumn($existing);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_reviews', 'reviewer_email')) {
                $table->string('reviewer_email')->nullable();
            }
            if (! Schema::hasColumn('customer_reviews', 'reviewer_phone')) {
                $table->string('reviewer_phone')->nullable();
            }
            if (! Schema::hasColumn('customer_reviews', 'reviewer_designation')) {
                $table->string('reviewer_designation')->nullable();
            }
            if (! Schema::hasColumn('customer_reviews', 'reviewer_company')) {
                $table->string('reviewer_company')->nullable();
            }
            if (! Schema::hasColumn('customer_reviews', 'slug')) {
                $table->string('slug')->nullable();
            }
        });
    }
};
