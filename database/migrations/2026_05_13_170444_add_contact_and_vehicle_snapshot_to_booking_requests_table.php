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
        Schema::table('booking_requests', function (Blueprint $table): void {
            $table->string('vehicle_brand', 100)->after('contact_email');
            $table->string('vehicle_model', 100)->after('vehicle_brand');
            $table->unsignedSmallInteger('vehicle_year')->after('vehicle_model');
            $table->string('vehicle_plate_number', 50)->nullable()->after('vehicle_year');
            $table->string('vehicle_vin', 50)->nullable()->after('vehicle_plate_number');

            $table->index(['workshop_id', 'contact_phone']);
        });
    }

    public function down(): void
    {
        Schema::table('booking_requests', function (Blueprint $table): void {
            $table->dropIndex(['workshop_id', 'contact_phone']);

            $table->dropColumn([
                'vehicle_brand',
                'vehicle_model',
                'vehicle_year',
                'vehicle_plate_number',
                'vehicle_vin',
            ]);
        });
    }
};
