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
        Schema::table('activities', function (Blueprint $table) {
            $table->json('old_values')->nullable()->after('description');
            $table->json('new_values')->nullable()->after('old_values');
            $table->string('ip_address')->nullable()->after('metadata');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('url')->nullable()->after('user_agent');
            $table->string('method')->nullable()->after('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn([
                'module',
                'old_values',
                'new_values',
                'ip_address',
                'user_agent',
                'url',
                'method'
            ]);
        });
    }
};
