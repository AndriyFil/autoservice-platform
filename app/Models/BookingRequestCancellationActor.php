<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name'])]
class BookingRequestCancellationActor extends Model
{
    public $incrementing = false;

    protected $keyType = 'int';

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'cancellation_actor_id');
    }
}
