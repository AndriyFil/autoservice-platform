<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BookingRequestStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

final class ListWorkshopBookingRequestsTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_booking_requests_for_workshop(): void
    {
        $this->seedBookingRequests();

        $response = $this->getJson('/api/workshops/garage-forsage/booking-requests');

        $response->assertOk();

        $this->assertResponseIds($response, [
            '44444444-4444-4444-4444-444444444444',
            '33333333-3333-3333-3333-333333333333',
            '22222222-2222-2222-2222-222222222222',
            '11111111-1111-1111-1111-111111111111',
        ]);
    }

    public function test_pagination_works(): void
    {
        $this->seedBookingRequests();

        $response = $this->getJson('/api/workshops/garage-forsage/booking-requests?per_page=2');

        $response
            ->assertOk()
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 4);

        $this->assertResponseIds($response, [
            '44444444-4444-4444-4444-444444444444',
            '33333333-3333-3333-3333-333333333333',
        ]);
    }

    public function test_filter_by_status(): void
    {
        $this->seedBookingRequests();

        $response = $this->getJson('/api/workshops/garage-forsage/booking-requests?status=confirmed');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.status', BookingRequestStatusEnum::Confirmed->value);

        $this->assertSingleResponseId($response, '22222222-2222-2222-2222-222222222222');
    }

    public function test_filter_by_customer_name(): void
    {
        $this->seedBookingRequests();

        $response = $this->getJson('/api/workshops/garage-forsage/booking-requests?customer_name=mar');

        $response->assertOk();

        $this->assertSingleResponseId($response, '33333333-3333-3333-3333-333333333333');
    }

    public function test_filter_by_customer_phone(): void
    {
        $this->seedBookingRequests();

        $response = $this->getJson('/api/workshops/garage-forsage/booking-requests?customer_phone=067');

        $response->assertOk();

        $this->assertSingleResponseId($response, '22222222-2222-2222-2222-222222222222');
    }

    public function test_filter_by_vehicle_plate_number(): void
    {
        $this->seedBookingRequests();

        $response = $this->getJson('/api/workshops/garage-forsage/booking-requests?vehicle_plate_number=CC3333');

        $response->assertOk();

        $this->assertSingleResponseId($response, '33333333-3333-3333-3333-333333333333');
    }

    public function test_filter_by_vehicle_vin(): void
    {
        $this->seedBookingRequests();

        $response = $this->getJson('/api/workshops/garage-forsage/booking-requests?vehicle_vin=VIN-BETA');

        $response->assertOk();

        $this->assertSingleResponseId($response, '22222222-2222-2222-2222-222222222222');
    }

    public function test_requests_from_another_workshop_are_not_returned(): void
    {
        $this->seedBookingRequests();

        $response = $this->getJson('/api/workshops/garage-forsage/booking-requests?customer_name=Other');

        $response->assertOk();

        $this->assertResponseIds($response, []);
    }

    /**
     * @param  array<int, string>  $expectedIds
     */
    private function assertResponseIds(TestResponse $response, array $expectedIds): void
    {
        $actualIds = collect($response->json('data'))
            ->pluck('id')
            ->all();

        $this->assertSame($expectedIds, $actualIds);
    }

    private function assertSingleResponseId(TestResponse $response, string $expectedId): void
    {
        $this->assertResponseIds($response, [$expectedId]);
    }

    private function seedBookingRequests(): void
    {
        $now = now();

        DB::table('workshops')->insert([
            [
                'id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
                'name' => 'Garage Forsage',
                'slug' => 'garage-forsage',
                'phone' => '+380501112233',
                'email' => 'garage@example.com',
                'timezone' => 'Europe/Kyiv',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
                'name' => 'Other Garage',
                'slug' => 'other-garage',
                'phone' => '+380501119999',
                'email' => 'other@example.com',
                'timezone' => 'Europe/Kyiv',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('customers')->insert([
            [
                'id' => 'aaaaaaaa-1111-1111-1111-aaaaaaaaaaaa',
                'workshop_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
                'name' => 'Ivan Petrenko',
                'phone' => '+380501111111',
                'email' => 'ivan@example.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'aaaaaaaa-2222-2222-2222-aaaaaaaaaaaa',
                'workshop_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
                'name' => 'Petro Ivanov',
                'phone' => '+380671111111',
                'email' => 'petro@example.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'aaaaaaaa-3333-3333-3333-aaaaaaaaaaaa',
                'workshop_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
                'name' => 'Maria Shevchenko',
                'phone' => '+380931111111',
                'email' => 'maria@example.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'aaaaaaaa-4444-4444-4444-aaaaaaaaaaaa',
                'workshop_id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
                'name' => 'Oleh Tkachenko',
                'phone' => '+380661111111',
                'email' => 'oleh@example.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'bbbbbbbb-1111-1111-1111-bbbbbbbbbbbb',
                'workshop_id' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
                'name' => 'Other Customer',
                'phone' => '+380501111111',
                'email' => 'other-customer@example.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('vehicles')->insert([
            [
                'id' => '11111111-aaaa-aaaa-aaaa-111111111111',
                'customer_id' => 'aaaaaaaa-1111-1111-1111-aaaaaaaaaaaa',
                'brand' => 'BMW',
                'model' => 'X5',
                'year' => 2018,
                'plate_number' => 'AA1111AA',
                'vin' => 'VIN-ALPHA',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => '22222222-aaaa-aaaa-aaaa-222222222222',
                'customer_id' => 'aaaaaaaa-2222-2222-2222-aaaaaaaaaaaa',
                'brand' => 'Audi',
                'model' => 'A6',
                'year' => 2019,
                'plate_number' => 'BB2222BB',
                'vin' => 'VIN-BETA',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => '33333333-aaaa-aaaa-aaaa-333333333333',
                'customer_id' => 'aaaaaaaa-3333-3333-3333-aaaaaaaaaaaa',
                'brand' => 'Toyota',
                'model' => 'Camry',
                'year' => 2020,
                'plate_number' => 'CC3333CC',
                'vin' => 'VIN-GAMMA',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => '44444444-aaaa-aaaa-aaaa-444444444444',
                'customer_id' => 'aaaaaaaa-4444-4444-4444-aaaaaaaaaaaa',
                'brand' => 'Ford',
                'model' => 'Focus',
                'year' => 2017,
                'plate_number' => 'DD4444DD',
                'vin' => 'VIN-DELTA',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => '99999999-bbbb-bbbb-bbbb-999999999999',
                'customer_id' => 'bbbbbbbb-1111-1111-1111-bbbbbbbbbbbb',
                'brand' => 'BMW',
                'model' => 'X3',
                'year' => 2021,
                'plate_number' => 'ZZ9999ZZ',
                'vin' => 'VIN-OTHER',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('booking_requests')->insert([
            $this->bookingRequestRow(
                id: '11111111-1111-1111-1111-111111111111',
                customerId: 'aaaaaaaa-1111-1111-1111-aaaaaaaaaaaa',
                vehicleId: '11111111-aaaa-aaaa-aaaa-111111111111',
                status: BookingRequestStatusEnum::New,
                contactName: 'Ivan Petrenko',
                contactPhone: '+380501111111',
                vehiclePlateNumber: 'AA1111AA',
                createdAt: $now->copy()->subMinutes(4),
            ),
            $this->bookingRequestRow(
                id: '22222222-2222-2222-2222-222222222222',
                customerId: 'aaaaaaaa-2222-2222-2222-aaaaaaaaaaaa',
                vehicleId: '22222222-aaaa-aaaa-aaaa-222222222222',
                status: BookingRequestStatusEnum::Confirmed,
                contactName: 'Petro Ivanov',
                contactPhone: '+380671111111',
                vehiclePlateNumber: 'BB2222BB',
                createdAt: $now->copy()->subMinutes(3),
            ),
            $this->bookingRequestRow(
                id: '33333333-3333-3333-3333-333333333333',
                customerId: 'aaaaaaaa-3333-3333-3333-aaaaaaaaaaaa',
                vehicleId: '33333333-aaaa-aaaa-aaaa-333333333333',
                status: BookingRequestStatusEnum::Rejected,
                contactName: 'Maria Shevchenko',
                contactPhone: '+380931111111',
                vehiclePlateNumber: 'CC3333CC',
                createdAt: $now->copy()->subMinutes(2),
            ),
            $this->bookingRequestRow(
                id: '44444444-4444-4444-4444-444444444444',
                customerId: 'aaaaaaaa-4444-4444-4444-aaaaaaaaaaaa',
                vehicleId: '44444444-aaaa-aaaa-aaaa-444444444444',
                status: BookingRequestStatusEnum::Cancelled,
                contactName: 'Oleh Tkachenko',
                contactPhone: '+380661111111',
                vehiclePlateNumber: 'DD4444DD',
                createdAt: $now->copy()->subMinute(),
            ),
            $this->bookingRequestRow(
                id: '99999999-9999-9999-9999-999999999999',
                workshopId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
                customerId: 'bbbbbbbb-1111-1111-1111-bbbbbbbbbbbb',
                vehicleId: '99999999-bbbb-bbbb-bbbb-999999999999',
                status: BookingRequestStatusEnum::New,
                contactName: 'Other Customer',
                contactPhone: '+380501111111',
                vehiclePlateNumber: 'ZZ9999ZZ',
                createdAt: $now,
            ),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function bookingRequestRow(
        string $id,
        string $customerId,
        string $vehicleId,
        BookingRequestStatusEnum $status,
        string $contactName,
        string $contactPhone,
        string $vehiclePlateNumber,
        mixed $createdAt,
        string $workshopId = 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
    ): array {
        return [
            'id' => $id,
            'workshop_id' => $workshopId,
            'workshop_service_id' => null,
            'customer_id' => $customerId,
            'vehicle_id' => $vehicleId,
            'booking_request_status_id' => $status->value,
            'contact_name' => $contactName,
            'contact_phone' => $contactPhone,
            'contact_email' => 'customer@example.com',
            'vehicle_brand' => 'BMW',
            'vehicle_model' => 'X5',
            'vehicle_year' => 2018,
            'vehicle_plate_number' => $vehiclePlateNumber,
            'preferred_date' => null,
            'preferred_time_window' => null,
            'customer_comment' => 'Suspension noise',
            'workshop_comment' => null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
