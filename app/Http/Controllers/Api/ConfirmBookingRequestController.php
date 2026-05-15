<?php

namespace App\Http\Controllers\Api;

use App\Actions\ConfirmBookingRequestAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingRequestResource;
use App\Models\BookingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class ConfirmBookingRequestController extends Controller
{
    public function __invoke(
        BookingRequest $bookingRequest,
        ConfirmBookingRequestAction $action,
    ): JsonResponse {
        $bookingRequest = $action->execute($bookingRequest);

        return (new BookingRequestResource($bookingRequest))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
