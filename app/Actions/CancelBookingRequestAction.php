<?php

namespace App\Actions;

use App\Data\CancelBookingRequestData;
use App\Models\BookingRequest;

class CancelBookingRequestAction
{
    public function execute(BookingRequest $bookingRequest, CancelBookingRequestData $data): BookingRequest
    {
        $bookingRequest->cancel($data->actor, $data->reason);
        $bookingRequest->save();

        return $bookingRequest;
    }
}
