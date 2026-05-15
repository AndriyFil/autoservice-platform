<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['workshop_id', 'name', 'description', 'estimated_duration_minutes', 'price_from', 'status'])]
class WorkshopService extends Model
{
    use HasUuids;

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
    }
}
