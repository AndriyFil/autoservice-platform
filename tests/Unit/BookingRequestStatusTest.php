<?php

namespace Tests\Unit;

use App\Enums\BookingRequestCancellationActorEnum;
use App\Enums\BookingRequestStatusEnum;
use App\Exceptions\BookingRequest\InvalidBookingRequestStatusTransitionException;
use App\Models\BookingRequest;
use Tests\TestCase;

class BookingRequestStatusTest extends TestCase
{
    public function test_new_booking_request_can_be_confirmed(): void
    {
        $bookingRequest = $this->bookingRequest(BookingRequestStatusEnum::New);

        $bookingRequest->confirm();

        $this->assertSame(BookingRequestStatusEnum::Confirmed->value, $bookingRequest->booking_request_status_id);
        $this->assertNotNull($bookingRequest->confirmed_at);
    }

    public function test_new_booking_request_can_be_rejected_with_reason(): void
    {
        $bookingRequest = $this->bookingRequest(BookingRequestStatusEnum::New);

        $bookingRequest->reject('No availability.');

        $this->assertSame(BookingRequestStatusEnum::Rejected->value, $bookingRequest->booking_request_status_id);
        $this->assertSame('No availability.', $bookingRequest->rejected_reason);
        $this->assertNotNull($bookingRequest->rejected_at);
    }

    public function test_new_booking_request_can_be_cancelled_by_customer(): void
    {
        $bookingRequest = $this->bookingRequest(BookingRequestStatusEnum::New);

        $bookingRequest->cancel(BookingRequestCancellationActorEnum::Customer, 'Customer changed plans.');

        $this->assertSame(BookingRequestStatusEnum::Cancelled->value, $bookingRequest->booking_request_status_id);
        $this->assertSame(BookingRequestCancellationActorEnum::Customer->id(), $bookingRequest->cancellation_actor_id);
        $this->assertSame('Customer changed plans.', $bookingRequest->cancelled_reason);
        $this->assertNotNull($bookingRequest->cancelled_at);
    }

    public function test_confirmed_booking_request_can_be_cancelled_by_workshop(): void
    {
        $bookingRequest = $this->bookingRequest(BookingRequestStatusEnum::Confirmed);

        $bookingRequest->cancel(BookingRequestCancellationActorEnum::Workshop, 'Lift is unavailable.');

        $this->assertSame(BookingRequestStatusEnum::Cancelled->value, $bookingRequest->booking_request_status_id);
        $this->assertSame(BookingRequestCancellationActorEnum::Workshop->id(), $bookingRequest->cancellation_actor_id);
        $this->assertSame('Lift is unavailable.', $bookingRequest->cancelled_reason);
        $this->assertNotNull($bookingRequest->cancelled_at);
    }

    public function test_rejected_booking_request_cannot_be_confirmed(): void
    {
        $bookingRequest = $this->bookingRequest(BookingRequestStatusEnum::Rejected);

        $this->expectException(InvalidBookingRequestStatusTransitionException::class);

        $bookingRequest->confirm();
    }

    public function test_cancelled_booking_request_cannot_be_confirmed(): void
    {
        $bookingRequest = $this->bookingRequest(BookingRequestStatusEnum::Cancelled);

        $this->expectException(InvalidBookingRequestStatusTransitionException::class);

        $bookingRequest->confirm();
    }

    public function test_rejected_booking_request_cannot_be_cancelled(): void
    {
        $bookingRequest = $this->bookingRequest(BookingRequestStatusEnum::Rejected);

        $this->expectException(InvalidBookingRequestStatusTransitionException::class);

        $bookingRequest->cancel(BookingRequestCancellationActorEnum::Customer);
    }

    public function test_cancelled_booking_request_cannot_be_cancelled_again(): void
    {
        $bookingRequest = $this->bookingRequest(BookingRequestStatusEnum::Cancelled);

        $this->expectException(InvalidBookingRequestStatusTransitionException::class);

        $bookingRequest->cancel(BookingRequestCancellationActorEnum::Customer);
    }

    private function bookingRequest(BookingRequestStatusEnum $status): BookingRequest
    {
        return new BookingRequest([
            'booking_request_status_id' => $status->value,
        ]);
    }
}
