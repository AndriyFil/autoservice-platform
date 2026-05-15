<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateBookingRequestAction;
use App\Data\CreateBookingRequestData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequestRequest;
use App\Http\Resources\BookingRequestResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class BookingRequestController extends Controller
{
    public function store(
        StoreBookingRequestRequest $request,
        string $workshop,
        CreateBookingRequestAction $action,
    ): JsonResponse {
        $bookingRequest = $action->execute(
            CreateBookingRequestData::fromRequest(
                request: $request,
                workshopSlug: $workshop,
            ),
        );

        return (new BookingRequestResource($bookingRequest))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
