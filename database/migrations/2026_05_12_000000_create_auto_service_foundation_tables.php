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
        Schema::create('workshops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('timezone')->default('Europe/Kyiv');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('workshop_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workshop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->timestamps();

            $table->unique(['workshop_id', 'user_id']);
        });

        Schema::create('workshop_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workshop_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->integer('price_from')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workshop_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->timestamps();

            $table->index(['workshop_id', 'phone']);
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->integer('year')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('vin')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_request_statuses', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });

        $now = now();

        DB::table('booking_request_statuses')->insert([
            ['id' => 1, 'code' => 'new', 'name' => 'New', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'code' => 'confirmed', 'name' => 'Confirmed', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'code' => 'reschedule_requested', 'name' => 'Reschedule requested', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'code' => 'rejected', 'name' => 'Rejected', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'code' => 'cancelled', 'name' => 'Cancelled', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'code' => 'converted_to_repair_order', 'name' => 'Converted to repair order', 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::create('booking_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workshop_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('workshop_service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('booking_request_status_id')->default(1);
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time_window')->nullable();
            $table->date('proposed_date')->nullable();
            $table->string('proposed_time_window')->nullable();
            $table->text('customer_comment')->nullable();
            $table->text('workshop_comment')->nullable();
            $table->timestamps();

            $table->foreign('booking_request_status_id')
                ->references('id')
                ->on('booking_request_statuses')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
        Schema::dropIfExists('booking_request_statuses');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('workshop_services');
        Schema::dropIfExists('workshop_users');
        Schema::dropIfExists('workshops');
    }
};
