<?php

namespace App\Http\Controllers\Api;

use App\Actions\RejectBookingRequestAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\RejectBookingRequestRequest;
use App\Http\Resources\BookingRequestResource;
use App\Models\BookingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class RejectBookingRequestController extends Controller
{
    public function __invoke(
        RejectBookingRequestRequest $request,
        BookingRequest $bookingRequest,
        RejectBookingRequestAction $action,
    ): JsonResponse {
        $bookingRequest = $action->execute(
            bookingRequest: $bookingRequest,
            reason: $request->string('reason')->toString(),
        );

        return (new BookingRequestResource($bookingRequest))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
