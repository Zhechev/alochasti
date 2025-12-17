<?php

namespace App\Enums;

/**
 * Item status for the gifting board.
 */
enum ItemStatus: string
{
    case Available = 'available';
    case Gifted = 'gifted';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Gifted => 'Gifted',
        };
    }
}


