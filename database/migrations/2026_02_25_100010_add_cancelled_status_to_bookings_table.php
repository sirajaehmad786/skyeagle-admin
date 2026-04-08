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
        // Adjust enum to add 'cancelled' while preserving existing values
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN status ENUM('confirmed','on_trip','completed','cancelled') 
            NOT NULL DEFAULT 'confirmed'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values (without 'cancelled')
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN status ENUM('confirmed','on_trip','completed') 
            NOT NULL DEFAULT 'confirmed'
        ");
    }
};

