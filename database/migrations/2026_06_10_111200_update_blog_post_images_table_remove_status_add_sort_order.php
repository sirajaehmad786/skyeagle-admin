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
        Schema::table('blog_post_images', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->unsignedInteger('sort_order')->default(0)->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_post_images', function (Blueprint $table) {
            $table->boolean('status')->default(1)->comment('1=Active, 0=Inactive')->after('image');
            $table->dropColumn('sort_order');
        });
    }
};
