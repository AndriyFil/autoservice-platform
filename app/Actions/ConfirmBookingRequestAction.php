<?php

namespace App\Actions;

use App\Models\BookingRequest;

class ConfirmBookingRequestAction
{
    public function execute(BookingRequest $bookingRequest): BookingRequest
    {
        $bookingRequest->confirm();
        $bookingRequest->save();

        return $bookingRequest;
    }
}
