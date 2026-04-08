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
        Schema::create('quotation_visas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('visa_country')->nullable();
            $table->string('visa_category')->nullable();
            $table->date('visa_travel_date')->nullable();
            $table->integer('visa_adults')->nullable();
            $table->integer('visa_child')->nullable();
            $table->integer('visa_infant')->nullable();
            $table->decimal('price',10,2)->default(0);
            $table->text('visa_remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_visas');
    }
};
