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
        Schema::create('quotation_flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
            $table->string('travel_mode')->nullable();
            $table->string('trip_type')->nullable();
            $table->string('flight_source_city')->nullable();
            $table->string('flight_destination_city')->nullable();
            $table->date('flight_start_date')->nullable();
            $table->date('flight_end_date')->nullable();
            $table->decimal('price',10,2)->nullable();
            $table->integer('flight_adults')->nullable();
            $table->integer('flight_child')->nullable();
            $table->integer('flight_infant')->nullable();
            $table->string('flight_class')->nullable();
            $table->text('flight_remarks')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_flights');
    }
};
