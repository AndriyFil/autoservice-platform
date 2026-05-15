<?php

namespace App\Actions;

use App\Data\CreateBookingRequestData;
use App\Enums\BookingRequestStatusEnum;
use App\Models\BookingRequest;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Workshop;

class CreateBookingRequestAction
{
    public function execute(CreateBookingRequestData $data): BookingRequest
    {
        $workshop = Workshop::query()->where('slug', $data->workshopSlug)->firstOrFail();
        if (! $workshop) {
            throw new \InvalidArgumentException('Workshop not found.');
        }

        $customer = Customer::query()->firstOrCreate(
            [
                'workshop_id' => $workshop->id,
                'phone' => $data->customerPhone,
            ],
            [
                'name' => $data->customerName,
                'email' => $data->customerEmail,
            ],
        );

        $vehicle = $this->findOrCreateVehicle($customer->id, $data);

        return BookingRequest::query()->create([
            'workshop_id' => $workshop->id,
            'workshop_service_id' => null,
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'booking_request_status_id' => BookingRequestStatusEnum::New->value,

            'contact_name' => $data->customerName,
            'contact_phone' => $data->customerPhone,
            'contact_email' => $data->customerEmail,

            'vehicle_brand' => $data->vehicleBrand,
            'vehicle_model' => $data->vehicleModel,
            'vehicle_year' => $data->vehicleYear,
            'vehicle_plate_number' => $data->vehiclePlateNumber,

            'preferred_date' => $data->preferredDate,
            'preferred_time_window' => null,
            'customer_comment' => $data->problemDescription,
        ]);
    }

    private function findOrCreateVehicle(string $customerId, CreateBookingRequestData $data): Vehicle
    {
        if ($data->vehiclePlateNumber !== null) {
            return Vehicle::query()->firstOrCreate(
                [
                    'customer_id' => $customerId,
                    'plate_number' => $data->vehiclePlateNumber,
                ],
                [
                    'brand' => $data->vehicleBrand,
                    'model' => $data->vehicleModel,
                    'year' => $data->vehicleYear,
                ],
            );
        }

        return Vehicle::query()->firstOrCreate([
            'customer_id' => $customerId,
            'brand' => $data->vehicleBrand,
            'model' => $data->vehicleModel,
            'year' => $data->vehicleYear,
        ]);
    }
}
