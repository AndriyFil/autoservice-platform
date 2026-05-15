<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BookingRequestStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CreateBookingRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_booking_request(): void
    {
        $this->seedBookingRequestDependencies();

        $response = $this->postJson('/api/workshops/garage-forsage/booking-requests', $this->validPayload([
            'customer_name' => 'Іван',
            'customer_phone' => '+380501234567',
            'customer_email' => 'ivan@example.com',
            'vehicle_plate_number' => 'AA1234BB',
            'problem_description' => 'Стук у передній підвісці',
        ]));

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', BookingRequestStatusEnum::New->value)
            ->assertJsonPath('data.contact.name', 'Іван')
            ->assertJsonPath('data.contact.phone', '+380501234567')
            ->assertJsonPath('data.contact.email', 'ivan@example.com')
            ->assertJsonPath('data.vehicle.brand', 'BMW')
            ->assertJsonPath('data.vehicle.model', 'X5')
            ->assertJsonPath('data.vehicle.year', 2018)
            ->assertJsonPath('data.vehicle.plate_number', 'AA1234BB')
            ->assertJsonPath('data.request.problem_description', 'Стук у передній підвісці')
            ->assertJsonPath('data.request.preferred_date', null)
            ->assertJsonMissingPath('data.preferred_time_window')
            ->assertJsonMissingPath('data.request.preferred_time_window');

        $this->assertDatabaseHas('customers', [
            'workshop_id' => '11111111-1111-1111-1111-111111111111',
            'phone' => '+380501234567',
            'name' => 'Іван',
            'email' => 'ivan@example.com',
        ]);

        $this->assertDatabaseHas('vehicles', [
            'brand' => 'BMW',
            'model' => 'X5',
            'year' => 2018,
            'plate_number' => 'AA1234BB',
        ]);

        $vehicleId = DB::table('vehicles')
            ->where('plate_number', 'AA1234BB')
            ->value('id');

        $this->assertDatabaseHas('booking_requests', [
            'workshop_id' => '11111111-1111-1111-1111-111111111111',
            'vehicle_id' => $vehicleId,
            'booking_request_status_id' => 1,
            'contact_name' => 'Іван',
            'contact_phone' => '+380501234567',
            'contact_email' => 'ivan@example.com',
            'vehicle_brand' => 'BMW',
            'vehicle_model' => 'X5',
            'vehicle_year' => 2018,
            'vehicle_plate_number' => 'AA1234BB',
            'customer_comment' => 'Стук у передній підвісці',
            'preferred_date' => null,
            'preferred_time_window' => null,
        ]);
    }

    public function test_customer_can_create_booking_request_without_preferred_date(): void
    {
        $this->seedBookingRequestDependencies();

        $response = $this->postJson('/api/workshops/garage-forsage/booking-requests', $this->validPayload());

        $response
            ->assertCreated()
            ->assertJsonPath('data.request.preferred_date', null)
            ->assertJsonMissingPath('data.request.preferred_time_window');

        $this->assertDatabaseHas('booking_requests', [
            'workshop_id' => '11111111-1111-1111-1111-111111111111',
            'contact_phone' => '+380501234567',
            'preferred_date' => null,
            'preferred_time_window' => null,
        ]);
    }

    public function test_preferred_date_is_saved_when_provided(): void
    {
        $this->seedBookingRequestDependencies();

        $preferredDate = now()->addDay()->toDateString();

        $response = $this->postJson('/api/workshops/garage-forsage/booking-requests', $this->validPayload([
            'preferred_date' => $preferredDate,
        ]));

        $response
            ->assertCreated()
            ->assertJsonPath('data.request.preferred_date', $preferredDate)
            ->assertJsonMissingPath('data.request.preferred_time_window');

        $this->assertDatabaseHas('booking_requests', [
            'workshop_id' => '11111111-1111-1111-1111-111111111111',
            'contact_phone' => '+380501234567',
            'preferred_date' => $preferredDate,
            'preferred_time_window' => null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Іван',
            'customer_phone' => '+380501234567',
            'customer_email' => 'ivan@example.com',
            'vehicle_brand' => 'BMW',
            'vehicle_model' => 'X5',
            'vehicle_year' => 2018,
            'vehicle_plate_number' => 'AA1234BB',
            'problem_description' => 'Стук у передній підвісці',
        ], $overrides);
    }

    private function seedBookingRequestDependencies(): void
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
    }
}
