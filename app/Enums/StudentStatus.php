<?php

declare(strict_types=1);

namespace App\Enums;

enum StudentStatus: string
{
    case Lulus = 'lulus';
    case TidakLulus = 'tidak_lulus';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::Lulus => 'Lulus',
            self::TidakLulus => 'Tidak Lulus',
            self::Pending => 'Pending',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Lulus => 'green',
            self::TidakLulus => 'red',
            self::Pending => 'yellow',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Lulus => 'check-circle',
            self::TidakLulus => 'x-circle',
            self::Pending => 'clock',
        };
    }
}
