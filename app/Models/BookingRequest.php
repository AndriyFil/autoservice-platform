<?php

namespace App\Models;

use App\Enums\BookingRequestCancellationActorEnum;
use App\Enums\BookingRequestStatusEnum;
use App\Exceptions\BookingRequest\InvalidBookingRequestStatusTransitionException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'workshop_id',
    'workshop_service_id',
    'customer_id',
    'vehicle_id',
    'booking_request_status_id',
    'preferred_date',
    'preferred_time_window',
    'proposed_date',
    'proposed_time_window',
    'customer_comment',
    'workshop_comment',

    'contact_name',
    'contact_phone',
    'contact_email',

    'vehicle_brand',
    'vehicle_model',
    'vehicle_year',
    'vehicle_plate_number',

    'confirmed_at',
    'rejected_at',
    'cancelled_at',
    'rejected_reason',
    'cancelled_reason',
    'cancellation_actor_id',
])]
class BookingRequest extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function workshopService(): BelongsTo
    {
        return $this->belongsTo(WorkshopService::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function bookingRequestStatus(): BelongsTo
    {
        return $this->belongsTo(BookingRequestStatus::class);
    }

    public function cancellationActor(): BelongsTo
    {
        return $this->belongsTo(BookingRequestCancellationActor::class, 'cancellation_actor_id');
    }

    public function confirm(): void
    {
        if ($this->booking_request_status_id !== BookingRequestStatusEnum::New->value) {
            throw new InvalidBookingRequestStatusTransitionException('Only new booking requests can be confirmed.');
        }

        $this->booking_request_status_id = BookingRequestStatusEnum::Confirmed->value;
        $this->confirmed_at = now();
    }

    public function reject(string $reason): void
    {
        if ($this->booking_request_status_id !== BookingRequestStatusEnum::New->value) {
            throw new InvalidBookingRequestStatusTransitionException('Only new booking requests can be rejected.');
        }

        $this->booking_request_status_id = BookingRequestStatusEnum::Rejected->value;
        $this->rejected_reason = $reason;
        $this->rejected_at = now();
    }

    public function cancel(BookingRequestCancellationActorEnum $actor, ?string $reason = null): void
    {
        if (! $this->canBeCancelled()) {
            throw new InvalidBookingRequestStatusTransitionException('Only new or confirmed booking requests can be cancelled.');
        }

        $this->booking_request_status_id = BookingRequestStatusEnum::Cancelled->value;
        $this->cancellation_actor_id = $actor->id();
        $this->cancelled_reason = $reason;
        $this->cancelled_at = now();
    }

    private function canBeCancelled(): bool
    {
        return in_array($this->booking_request_status_id, [
            BookingRequestStatusEnum::New->value,
            BookingRequestStatusEnum::Confirmed->value,
        ], true);
    }
}
