<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BookingRequestCancellationActorEnum;
use App\Enums\BookingRequestStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class BookingRequestTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_confirm_changes_new_to_confirmed(): void
    {
        $this->seedBookingRequestDependencies();

        $response = $this->postJson('/api/booking-requests/44444444-4444-4444-4444-444444444444/confirm');

        $response
            ->assertOk()
            ->assertJsonPath('data.status', BookingRequestStatusEnum::Confirmed->value);

        $this->assertDatabaseHas('booking_requests', [
            'id' => '44444444-4444-4444-4444-444444444444',
            'booking_request_status_id' => BookingRequestStatusEnum::Confirmed->value,
        ]);

        $this->assertNotNull(DB::table('booking_requests')->value('confirmed_at'));
    }

    public function test_post_reject_changes_new_to_rejected_and_saves_reason(): void
    {
        $this->seedBookingRequestDependencies();

        $response = $this->postJson('/api/booking-requests/44444444-4444-4444-4444-444444444444/reject', [
            'reason' => 'No available slot for requested work.',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', BookingRequestStatusEnum::Rejected->value)
            ->assertJsonPath('data.rejected_reason', 'No available slot for requested work.');

        $this->assertDatabaseHas('booking_requests', [
            'id' => '44444444-4444-4444-4444-444444444444',
            'booking_request_status_id' => BookingRequestStatusEnum::Rejected->value,
            'rejected_reason' => 'No available slot for requested work.',
        ]);

        $this->assertNotNull(DB::table('booking_requests')->value('rejected_at'));
    }

    public function test_post_cancel_changes_new_to_cancelled_and_saves_actor_and_reason(): void
    {
        $this->seedBookingRequestDependencies();

        $response = $this->postJson('/api/booking-requests/44444444-4444-4444-4444-444444444444/cancel', [
            'actor' => BookingRequestCancellationActorEnum::Customer->value,
            'reason' => 'Customer no longer needs service.',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', BookingRequestStatusEnum::Cancelled->value)
            ->assertJsonPath('data.cancelled_reason', 'Customer no longer needs service.')
            ->assertJsonPath('data.cancellation_actor.code', BookingRequestCancellationActorEnum::Customer->value)
            ->assertJsonPath('data.cancellation_actor.name', 'Customer');

        $this->assertDatabaseHas('booking_requests', [
            'id' => '44444444-4444-4444-4444-444444444444',
            'booking_request_status_id' => BookingRequestStatusEnum::Cancelled->value,
            'cancellation_actor_id' => BookingRequestCancellationActorEnum::Customer->id(),
            'cancelled_reason' => 'Customer no longer needs service.',
        ]);

        $this->assertNotNull(DB::table('booking_requests')->value('cancelled_at'));
    }

    public function test_invalid_transition_returns_conflict(): void
    {
        $this->seedBookingRequestDependencies(BookingRequestStatusEnum::Rejected);

        $response = $this->postJson('/api/booking-requests/44444444-4444-4444-4444-444444444444/cancel', [
            'actor' => BookingRequestCancellationActorEnum::Customer->value,
        ]);

        $response->assertConflict();
    }

    public function test_reject_without_reason_returns_validation_error(): void
    {
        $this->seedBookingRequestDependencies();

        $response = $this->postJson('/api/booking-requests/44444444-4444-4444-4444-444444444444/reject');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('reason');
    }

    public function test_cancel_without_actor_returns_validation_error(): void
    {
        $this->seedBookingRequestDependencies();

        $response = $this->postJson('/api/booking-requests/44444444-4444-4444-4444-444444444444/cancel');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('actor');
    }

    public function test_cancel_with_invalid_actor_returns_validation_error(): void
    {
        $this->seedBookingRequestDependencies();

        $response = $this->postJson('/api/booking-requests/44444444-4444-4444-4444-444444444444/cancel', [
            'actor' => 'partner',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('actor');
    }

    private function seedBookingRequestDependencies(
        BookingRequestStatusEnum $status = BookingRequestStatusEnum::New,
    ): void {
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
            'name' => 'Ivan',
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
            'booking_request_status_id' => $status->value,
            'contact_name' => 'Ivan',
            'contact_phone' => '+380501234567',
            'contact_email' => 'ivan@example.com',
            'vehicle_brand' => 'BMW',
            'vehicle_model' => 'X5',
            'vehicle_year' => 2018,
            'vehicle_plate_number' => 'AA1234BB',
            'preferred_date' => null,
            'preferred_time_window' => null,
            'customer_comment' => 'Suspension noise',
            'workshop_comment' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
