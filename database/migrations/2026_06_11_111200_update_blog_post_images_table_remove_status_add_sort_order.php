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
            if (Schema::hasColumn('blog_post_images', 'status')) {
                $table->dropColumn('status');
            }
            if (! Schema::hasColumn('blog_post_images', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_post_images', function (Blueprint $table) {
            if (! Schema::hasColumn('blog_post_images', 'status')) {
                $table->boolean('status')->default(1)->comment('1=Active, 0=Inactive')->after('image');
            }
            if (Schema::hasColumn('blog_post_images', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
