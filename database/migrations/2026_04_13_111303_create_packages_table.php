<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('package_name', 255);
            $table->string('slug', 191)->unique();
            $table->string('package_code', 100)->unique();
            $table->unsignedBigInteger('source_city_id')->index();
            $table->unsignedBigInteger('destination_city_id')->index();
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('min_people');
            $table->unsignedInteger('max_people');
            $table->date('start_date');
            $table->date('end_date');
            $table->longText('description');
            $table->longText('inclusions')->nullable();
            $table->longText('exclusions')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->unsignedBigInteger('created_by')->index();
            $table->softDeletes();
            $table->timestamps();
        });
        try {
            DB::statement('ALTER TABLE packages ADD CONSTRAINT chk_people CHECK (max_people >= min_people)');
        } catch (\Exception $e) {}
        try {
            DB::statement('ALTER TABLE packages ADD CONSTRAINT chk_dates CHECK (end_date >= start_date)');
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE packages DROP CHECK chk_people');
        } catch (\Exception $e) {}
        try {
            DB::statement('ALTER TABLE packages DROP CHECK chk_dates');
        } catch (\Exception $e) {}
        Schema::dropIfExists('packages');
    }
};
