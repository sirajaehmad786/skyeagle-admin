<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'visa_adult_service_charge')) {
                $table->decimal('visa_adult_service_charge', 10, 2)
                    ->default(0)
                    ->after('amount_description_services');
            }

            if (!Schema::hasColumn('quotations', 'visa_child_service_charge')) {
                $table->decimal('visa_child_service_charge', 10, 2)
                    ->default(0)
                    ->after('visa_adult_service_charge');
            }
        });

        if (Schema::hasColumn('quotations', 'visa_service_price')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->dropColumn('visa_service_price');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('quotations', 'visa_service_price')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->decimal('visa_service_price', 10, 2)
                    ->default(0)
                    ->after('amount_description_services');
            });
        }

        Schema::table('quotations', function (Blueprint $table) {
            $dropColumns = [];
            if (Schema::hasColumn('quotations', 'visa_adult_service_charge')) {
                $dropColumns[] = 'visa_adult_service_charge';
            }
            if (Schema::hasColumn('quotations', 'visa_child_service_charge')) {
                $dropColumns[] = 'visa_child_service_charge';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
