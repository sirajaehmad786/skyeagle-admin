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
        Schema::create('customer_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('review_title')->nullable();
            $table->text('review_description');
            $table->string('reviewer_name');
            $table->string('reviewer_email')->nullable();
            $table->string('reviewer_phone')->nullable();
            $table->string('reviewer_designation')->nullable();
            $table->string('reviewer_company')->nullable();
            $table->string('reviewer_location')->nullable();
            $table->string('reviewer_image')->nullable();
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->integer('sort_order')->default(0);
            $table->string('slug')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_reviews');
    }
};
