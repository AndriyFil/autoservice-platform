<?php

namespace App\Queries;

use App\Data\BookingRequestFiltersData;
use App\Enums\BookingRequestStatusEnum;
use App\Models\BookingRequest;
use App\Models\Workshop;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListWorkshopBookingRequestsQuery
{
    /**
     * @return LengthAwarePaginator<int, BookingRequest>
     */
    public function execute(string $workshopSlug, BookingRequestFiltersData $filters): LengthAwarePaginator
    {
        $workshop = Workshop::query()
            ->where('slug', $workshopSlug)
            ->firstOrFail();

        $query = BookingRequest::query()
            ->with('cancellationActor')
            ->where('workshop_id', $workshop->id);

        if ($filters->status !== null) {
            $query->where(
                'booking_request_status_id',
                BookingRequestStatusEnum::fromCode($filters->status)->value,
            );
        }

        $this->applyLikeFilter($query, 'contact_name', $filters->customerName);
        $this->applyLikeFilter($query, 'contact_phone', $filters->customerPhone);
        $this->applyLikeFilter($query, 'vehicle_plate_number', $filters->vehiclePlateNumber);

        if ($filters->vehicleVin !== null) {
            $query->whereHas('vehicle', function (Builder $query) use ($filters): void {
                $this->applyLikeFilter($query, 'vin', $filters->vehicleVin);
            });
        }

        return $query
            ->latest()
            ->paginate($filters->perPage);
    }

    /**
     * @param Builder<*> $query
     */
    private function applyLikeFilter(Builder $query, string $column, ?string $value): void
    {
        if ($value === null) {
            return;
        }

        $query->where(
            $column,
            $this->likeOperator(),
            '%'.$this->escapeLikeValue($value).'%',
        );
    }

    private function likeOperator(): string
    {
        return DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
    }

    private function escapeLikeValue(string $value): string
    {
        return addcslashes($value, '\\%_');
    }
}
