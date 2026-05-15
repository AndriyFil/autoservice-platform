<?php

namespace App\Data;

use App\Http\Requests\StoreBookingRequestRequest;

final readonly class CreateBookingRequestData
{
    public function __construct(
        public string $workshopSlug,
        public string $customerName,
        public string $customerPhone,
        public ?string $customerEmail,
        public string $vehicleBrand,
        public string $vehicleModel,
        public int $vehicleYear,
        public ?string $vehiclePlateNumber,
        public string $problemDescription,
        public ?string $preferredDate,
    ) {}

    public static function fromRequest(
        StoreBookingRequestRequest $request,
        string $workshopSlug,
    ): self {
        $validated = $request->validated();

        return new self(
            workshopSlug: $workshopSlug,
            customerName: $validated['customer_name'],
            customerPhone: $validated['customer_phone'],
            customerEmail: $validated['customer_email'] ?? null,
            vehicleBrand: $validated['vehicle_brand'],
            vehicleModel: $validated['vehicle_model'],
            vehicleYear: (int) $validated['vehicle_year'],
            vehiclePlateNumber: $validated['vehicle_plate_number'] ?? null,
            problemDescription: $validated['problem_description'],
            preferredDate: $validated['preferred_date'] ?? null,
        );
    }
}
