<?php

namespace App\Actions;

use App\Models\BookingRequest;

class RejectBookingRequestAction
{
    public function execute(BookingRequest $bookingRequest, string $reason): BookingRequest
    {
        $bookingRequest->reject($reason);
        $bookingRequest->save();

        return $bookingRequest;
    }
}
