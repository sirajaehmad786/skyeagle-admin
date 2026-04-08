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
        Schema::create('quotation_hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained('hotels');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->integer('total_room')->default(0);
            $table->decimal('price',10,2)->nullable();
            $table->string('meals')->nullable();
            $table->string('room_type')->nullable();
            $table->string('destination')->nullable();
            $table->integer('total_adult')->default(0);
            $table->integer('total_cwb')->default(0);
            $table->integer('total_cnb')->default(0);
            $table->text('hotel_remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_hotels', function (Blueprint $table) {
            $table->dropForeign(['hotel_id']);
        });

        Schema::dropIfExists('quotation_hotels');
    }
};
