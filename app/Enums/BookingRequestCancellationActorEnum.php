<?php

declare(strict_types=1);

namespace App\Enums;

enum BookingRequestCancellationActorEnum: string
{
    case Customer = 'customer';
    case Workshop = 'workshop';
    case System = 'system';

    public function id(): int
    {
        return match ($this) {
            self::Customer => 1,
            self::Workshop => 2,
            self::System => 3,
        };
    }

    public static function fromId(int $id): self
    {
        return match ($id) {
            1 => self::Customer,
            2 => self::Workshop,
            3 => self::System,
        };
    }
}
