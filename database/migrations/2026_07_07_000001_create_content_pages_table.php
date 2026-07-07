<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $pages = [
            ['title' => 'Privacy Policy', 'slug' => 'privacy-policy', 'sort_order' => 1],
            ['title' => 'Terms and Conditions', 'slug' => 'terms-conditions', 'sort_order' => 2],
            ['title' => 'Refund Policy', 'slug' => 'refund-policy', 'sort_order' => 3],
        ];

        foreach ($pages as $page) {
            DB::table('content_pages')->insert([
                'title' => $page['title'],
                'slug' => $page['slug'] ?: Str::slug($page['title']),
                'content' => null,
                'is_active' => true,
                'sort_order' => $page['sort_order'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('content_pages');
    }
};
