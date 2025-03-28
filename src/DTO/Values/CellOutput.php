<?php declare(strict_types=1);

namespace App\DTO\Values;

enum CellOutput: string
{
    case OUTPUT_SQUARE_OPEN = ' ';
    case OUTPUT_SQUARE_FILLED = '▇';
    case OUTPUT_SQUARE_IGNORED = 'ᚷ';

    public static function fromGridSquareValue(int|CellValue $value): self
    {
        return match ($value) {
            CellValue::SQUARE_OPEN, CellValue::SQUARE_OPEN->value => self::OUTPUT_SQUARE_OPEN,
            CellValue::SQUARE_FILLED, CellValue::SQUARE_FILLED->value => self::OUTPUT_SQUARE_FILLED,
            CellValue::SQUARE_IGNORED, CellValue::SQUARE_IGNORED->value => self::OUTPUT_SQUARE_IGNORED,
        };
    }
}
