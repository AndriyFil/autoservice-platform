<?php

declare(strict_types=1);

namespace App\Enums;

enum BookingRequestStatusEnum: int
{
    case New = 1;
    case Confirmed = 2;
    case RescheduleRequested = 3;
    case Rejected = 4;
    case Cancelled = 5;
    case ConvertedToRepairOrder = 6;

    public function code(): string
    {
        return match ($this) {
            self::New => 'new',
            self::Confirmed => 'confirmed',
            self::RescheduleRequested => 'reschedule_requested',
            self::Rejected => 'rejected',
            self::Cancelled => 'cancelled',
            self::ConvertedToRepairOrder => 'converted_to_repair_order',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Confirmed => 'Confirmed',
            self::RescheduleRequested => 'Reschedule requested',
            self::Rejected => 'Rejected',
            self::Cancelled => 'Cancelled',
            self::ConvertedToRepairOrder => 'Converted to repair order',
        };
    }

    public static function fromCode(string $code): self
    {
        return match ($code) {
            'new' => self::New,
            'confirmed' => self::Confirmed,
            'reschedule_requested' => self::RescheduleRequested,
            'rejected' => self::Rejected,
            'cancelled' => self::Cancelled,
            'converted_to_repair_order' => self::ConvertedToRepairOrder,
        };
    }
}
