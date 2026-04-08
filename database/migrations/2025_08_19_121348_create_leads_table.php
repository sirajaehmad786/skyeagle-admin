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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade');
            $table->string('query_type',100)->nullable();
            $table->json('destination')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedSmallInteger('no_of_kids')->default(0);
            $table->unsignedSmallInteger('no_of_adults')->default(0);
            $table->string('food_preference', 50)->nullable();
            $table->string('meals', 50)->nullable();
            $table->text('additional_note')->nullable();
            $table->text('hotel_category')->nullable();
            $table->string('customer_category', 50)->nullable();
            $table->string('visa_requirements', 50)->nullable();
            $table->string('flight_requirements', 50)->nullable();
            $table->string('flight_from', 100)->nullable();
            $table->string('flight_to', 100)->nullable();
            $table->string('travel_type')->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('gst_no', 50)->nullable();
            $table->string('pan_no', 50)->nullable();
            $table->string('tags', 20)->nullable();
            $table->string('remarks')->nullable();
            $table->string('lead_stage', 50)->nullable();
            $table->string('lead_status', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
