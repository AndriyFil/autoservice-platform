<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_request_cancellation_actors', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });

        $now = now();

        DB::table('booking_request_cancellation_actors')->insert([
            ['id' => 1, 'code' => 'customer', 'name' => 'Customer', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'code' => 'workshop', 'name' => 'Workshop', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'code' => 'system', 'name' => 'System', 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::table('booking_requests', function (Blueprint $table) {
            $table->timestamp('confirmed_at')->nullable()->after('booking_request_status_id');
            $table->timestamp('rejected_at')->nullable()->after('confirmed_at');
            $table->timestamp('cancelled_at')->nullable()->after('rejected_at');
            $table->text('rejected_reason')->nullable()->after('cancelled_at');
            $table->text('cancelled_reason')->nullable()->after('rejected_reason');
            $table->unsignedSmallInteger('cancellation_actor_id')->nullable()->after('cancelled_reason');

            $table->foreign('cancellation_actor_id')
                ->references('id')
                ->on('booking_request_cancellation_actors')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('booking_requests', function (Blueprint $table) {
            $table->dropForeign(['cancellation_actor_id']);
            $table->dropColumn([
                'confirmed_at',
                'rejected_at',
                'cancelled_at',
                'rejected_reason',
                'cancelled_reason',
                'cancellation_actor_id',
            ]);
        });

        Schema::dropIfExists('booking_request_cancellation_actors');
    }
};
