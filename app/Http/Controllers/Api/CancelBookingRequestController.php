<?php

namespace App\Http\Controllers\Api;

use App\Actions\CancelBookingRequestAction;
use App\Data\CancelBookingRequestData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CancelBookingRequestRequest;
use App\Http\Resources\BookingRequestResource;
use App\Models\BookingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class CancelBookingRequestController extends Controller
{
    public function __invoke(
        CancelBookingRequestRequest $request,
        BookingRequest $bookingRequest,
        CancelBookingRequestAction $action,
    ): JsonResponse {
        $bookingRequest = $action->execute(
            bookingRequest: $bookingRequest,
            data: CancelBookingRequestData::fromRequest($request),
        );

        return (new BookingRequestResource($bookingRequest))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
