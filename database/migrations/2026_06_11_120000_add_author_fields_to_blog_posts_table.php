<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('author_name', 150)->nullable()->after('featured_image');
            $table->string('author_image', 255)->nullable()->after('author_name');
            $table->text('author_about')->nullable()->after('author_image');
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['author_name', 'author_image', 'author_about']);
        });
    }
};
