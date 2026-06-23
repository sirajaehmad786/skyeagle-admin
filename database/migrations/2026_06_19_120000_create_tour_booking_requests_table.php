<?php

use App\Models\TourBookingRequest;
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
        Schema::create('tour_booking_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 150);
            $table->string('email', 150);
            $table->string('phone', 30)->nullable();
            $table->date('travel_from_date')->nullable();
            $table->date('travel_to_date')->nullable();
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);
            $table->unsignedInteger('infants')->default(0);
            $table->text('special_request')->nullable();
            $table->decimal('estimated_price', 12, 2)->nullable();
            $table->string('currency', 10)->default('INR');
            $table->string('package_name_snapshot', 255)->nullable();
            $table->string('package_code_snapshot', 100)->nullable();
            $table->decimal('package_price_snapshot', 12, 2)->nullable();
            $table->enum('status', TourBookingRequest::statuses())->default(TourBookingRequest::STATUS_PENDING);
            $table->text('admin_note')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('source', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['travel_from_date', 'travel_to_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_booking_requests');
    }
};
