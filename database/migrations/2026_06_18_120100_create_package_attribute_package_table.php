<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('package_attribute_package')) {
            return;
        }

        Schema::create('package_attribute_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('package_attribute_id')->constrained('package_attributes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['package_id', 'package_attribute_id'], 'package_attribute_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_attribute_package');
    }
};
