<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop legacy CRM tables removed from the application.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = [
            'quotation_flight_items',
            'quotation_flights',
            'quotation_sight_items',
            'quotation_sights',
            'quotation_visas',
            'quotation_hotels',
            'payments',
            'bookings',
            'quotations',
            'follow_ups',
            'lead_histories',
            'lead_history',
            'leads',
            'documents',
            'contacts',
            'hotels',
            'sight_seeing_master',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Tables were intentionally removed from the codebase.
    }
};
