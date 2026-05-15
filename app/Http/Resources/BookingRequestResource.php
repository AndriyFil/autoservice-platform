<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\BookingRequestCancellationActorEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BookingRequestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->booking_request_status_id,
            'confirmed_at' => $this->confirmed_at?->toISOString(),
            'rejected_at' => $this->rejected_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'rejected_reason' => $this->rejected_reason,
            'cancelled_reason' => $this->cancelled_reason,
            'cancellation_actor' => $this->cancellation_actor_id
                ? [
                    'code' => BookingRequestCancellationActorEnum::fromId($this->cancellation_actor_id)->value,
                    'name' => $this->cancellationActor?->name,
                ]
                : null,

            'contact' => [
                'name' => $this->contact_name,
                'phone' => $this->contact_phone,
                'email' => $this->contact_email,
            ],

            'vehicle' => [
                'brand' => $this->vehicle_brand,
                'model' => $this->vehicle_model,
                'year' => $this->vehicle_year,
                'plate_number' => $this->vehicle_plate_number,
            ],

            'request' => [
                'problem_description' => $this->customer_comment,
                'preferred_date' => $this->preferred_date,
            ],

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
