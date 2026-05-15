<?php

namespace App\Http\Controllers\Api;

use App\Data\BookingRequestFiltersData;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListBookingRequestsRequest;
use App\Http\Resources\BookingRequestResource;
use App\Queries\ListWorkshopBookingRequestsQuery;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class WorkshopBookingRequestController extends Controller
{
    public function index(
        ListBookingRequestsRequest $request,
        string $workshop,
        ListWorkshopBookingRequestsQuery $query,
    ): AnonymousResourceCollection {
        return BookingRequestResource::collection(
            $query->execute(
                workshopSlug: $workshop,
                filters: BookingRequestFiltersData::fromRequest($request),
            ),
        );
    }
}
