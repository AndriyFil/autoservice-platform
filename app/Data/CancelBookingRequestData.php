<?php

namespace App\Data;

use App\Enums\BookingRequestCancellationActorEnum;
use App\Http\Requests\CancelBookingRequestRequest;

final readonly class CancelBookingRequestData
{
    public function __construct(
        public BookingRequestCancellationActorEnum $actor,
        public ?string $reason,
    ) {}

    public static function fromRequest(CancelBookingRequestRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            actor: BookingRequestCancellationActorEnum::from($validated['actor']),
            reason: $validated['reason'] ?? null,
        );
    }
}
