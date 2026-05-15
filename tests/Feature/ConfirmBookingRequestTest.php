<?php

declare(strict_types=1);

namespace Feature;

use App\Enums\BookingRequestStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class ConfirmBookingRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_booking_request(): void
    {
        $this->seedBookingRequestDependencies();

        $response = $this->postJson(
            '/api/booking-requests/44444444-4444-4444-4444-444444444444/confirm',
        );

        $response->assertOk();

        $this->assertDatabaseHas('booking_requests', [
            'id' => '44444444-4444-4444-4444-444444444444',
            'booking_request_status_id' => BookingRequestStatusEnum::Confirmed->value,
        ]);
    }

    public function test_confirmed_booking_request_cannot_be_confirmed_again(): void
    {
        $this->seedBookingRequestDependencies();

        $response = $this->postJson(
            '/api/booking-requests/44444444-4444-4444-4444-444444444444/confirm',
        );

        $response->assertOk();

        $this->assertDatabaseHas('booking_requests', [
            'id' => '44444444-4444-4444-4444-444444444444',
            'booking_request_status_id' => BookingRequestStatusEnum::Confirmed->value,
        ]);

        $response = $this->postJson(
            '/api/booking-requests/44444444-4444-4444-4444-444444444444/confirm',
        );

        $response->assertConflict();

        $this->assertDatabaseHas('booking_requests', [
            'id' => '44444444-4444-4444-4444-444444444444',
            'booking_request_status_id' => BookingRequestStatusEnum::Confirmed->value,
        ]);
    }

    public function test_confirmed_booking_request_canceled_cannot_be_confirmed_again(): void
    {
        $this->seedBookingRequestDependencies(BookingRequestStatusEnum::Cancelled->value);

        $response = $this->postJson(
            '/api/booking-requests/44444444-4444-4444-4444-444444444444/confirm',
        );

        $response->assertConflict();

        $this->assertDatabaseHas('booking_requests', [
            'id' => '44444444-4444-4444-4444-444444444444',
            'booking_request_status_id' => BookingRequestStatusEnum::Cancelled->value,
        ]);
    }

    public function test_confirmed_booking_request_rejected_cannot_be_confirmed_again(): void
    {
        $this->seedBookingRequestDependencies(BookingRequestStatusEnum::Rejected->value);

        $response = $this->postJson(
            '/api/booking-requests/44444444-4444-4444-4444-444444444444/confirm',
        );

        $response->assertConflict();

        $this->assertDatabaseHas('booking_requests', [
            'id' => '44444444-4444-4444-4444-444444444444',
            'booking_request_status_id' => BookingRequestStatusEnum::Rejected->value,
        ]);
    }

    private function seedBookingRequestDependencies(int $statusId = BookingRequestStatusEnum::New->value): void
    {
        DB::table('workshops')->insert([
            'id' => '11111111-1111-1111-1111-111111111111',
            'name' => 'Garage Forsage',
            'slug' => 'garage-forsage',
            'phone' => '+380501112233',
            'email' => 'garage@example.com',
            'timezone' => 'Europe/Kyiv',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('customers')->insert([
            'id' => '22222222-2222-2222-2222-222222222222',
            'workshop_id' => '11111111-1111-1111-1111-111111111111',
            'name' => 'Іван',
            'phone' => '+380501234567',
            'email' => 'ivan@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('vehicles')->insert([
            'id' => '33333333-3333-3333-3333-333333333333',
            'customer_id' => '22222222-2222-2222-2222-222222222222',
            'brand' => 'BMW',
            'model' => 'X5',
            'year' => 2018,
            'plate_number' => 'AA1234BB',
            'vin' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('booking_requests')->insert([
            'id' => '44444444-4444-4444-4444-444444444444',
            'workshop_id' => '11111111-1111-1111-1111-111111111111',
            'workshop_service_id' => null,
            'customer_id' => '22222222-2222-2222-2222-222222222222',
            'vehicle_id' => '33333333-3333-3333-3333-333333333333',
            'booking_request_status_id' => $statusId,

            'contact_name' => 'Іван',
            'contact_phone' => '+380501234567',
            'contact_email' => 'ivan@example.com',

            'vehicle_brand' => 'BMW',
            'vehicle_model' => 'X5',
            'vehicle_year' => 2018,
            'vehicle_plate_number' => 'AA1234BB',

            'preferred_date' => null,
            'preferred_time_window' => null,
            'customer_comment' => 'Стук у підвісці',
            'workshop_comment' => null,

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
