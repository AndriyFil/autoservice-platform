<?php

namespace App\Data;

use App\Http\Requests\ListBookingRequestsRequest;

final readonly class BookingRequestFiltersData
{
    public function __construct(
        public ?string $status,
        public ?string $customerName,
        public ?string $customerPhone,
        public ?string $vehiclePlateNumber,
        public ?string $vehicleVin,
        public int $perPage,
    ) {}

    public static function fromRequest(ListBookingRequestsRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            status: $validated['status'] ?? null,
            customerName: $validated['customer_name'] ?? null,
            customerPhone: $validated['customer_phone'] ?? null,
            vehiclePlateNumber: $validated['vehicle_plate_number'] ?? null,
            vehicleVin: $validated['vehicle_vin'] ?? null,
            perPage: (int) ($validated['per_page'] ?? 15),
        );
    }
}
