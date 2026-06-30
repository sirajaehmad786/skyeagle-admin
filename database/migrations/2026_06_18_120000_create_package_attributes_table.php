<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('package_attributes')) {
            Schema::table('package_attributes', function (Blueprint $table) {
                if (! Schema::hasColumn('package_attributes', 'slug')) {
                    $table->string('slug')->nullable()->after('name')->index();
                }

                if (! Schema::hasColumn('package_attributes', 'sort_order')) {
                    $table->unsignedInteger('sort_order')->default(0)->after('slug');
                }

                if (! Schema::hasColumn('package_attributes', 'status')) {
                    $table->boolean('status')->default(true)->after('sort_order')->index();
                }

                if (! Schema::hasColumn('package_attributes', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }

                if (! Schema::hasColumn('package_attributes', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }

                if (! Schema::hasColumn('package_attributes', 'deleted_at')) {
                    $table->softDeletes();
                }
            });

            return;
        }

        Schema::create('package_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100)->index();
            $table->string('name');
            $table->string('slug')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['type', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_attributes');
    }
};
