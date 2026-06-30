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
        $this->ensureCustomerReviewColumns();
        $this->ensurePackageAttributeColumns();
    }

    public function down(): void
    {
        //
    }

    private function ensureCustomerReviewColumns(): void
    {
        if (! Schema::hasTable('customer_reviews')) {
            return;
        }

        Schema::table('customer_reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_reviews', 'package_id')) {
                $table->foreignId('package_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('packages')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('customer_reviews', 'is_active')) {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('sort_order')
                    ->index();
            }
        });
    }

    private function ensurePackageAttributeColumns(): void
    {
        if (! Schema::hasTable('package_attributes')) {
            return;
        }

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

        if (Schema::hasColumn('package_attributes', 'slug')) {
            DB::table('package_attributes')
                ->where(function ($query) {
                    $query->whereNull('slug')
                        ->orWhere('slug', '');
                })
                ->orderBy('id')
                ->select(['id', 'name'])
                ->chunkById(100, function ($attributes) {
                    foreach ($attributes as $attribute) {
                        DB::table('package_attributes')
                            ->where('id', $attribute->id)
                            ->update(['slug' => Str::slug($attribute->name)]);
                    }
                });
        }
    }
};
