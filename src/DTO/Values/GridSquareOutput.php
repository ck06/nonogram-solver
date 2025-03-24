<?php declare(strict_types=1);

namespace App\DTO\Values;

enum GridSquareOutput: string
{
    case OUTPUT_SQUARE_OPEN = ' ';
    case OUTPUT_SQUARE_FILLED = '▇';
    case OUTPUT_SQUARE_IGNORED = 'ᚷ';

    public static function fromGridSquareValue(int|GridSquareValue $value): self
    {
        return match ($value) {
            GridSquareValue::SQUARE_OPEN, GridSquareValue::SQUARE_OPEN->value => self::OUTPUT_SQUARE_OPEN,
            GridSquareValue::SQUARE_FILLED, GridSquareValue::SQUARE_FILLED->value => self::OUTPUT_SQUARE_FILLED,
            GridSquareValue::SQUARE_IGNORED, GridSquareValue::SQUARE_IGNORED->value => self::OUTPUT_SQUARE_IGNORED,
        };
    }
}
