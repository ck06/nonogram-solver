<?php declare(strict_types=1);

namespace App\DTO\Values;

enum CellValue: int
{
    case SQUARE_OPEN = 0;
    case SQUARE_FILLED = 1;
    case SQUARE_IGNORED = 2;
}
