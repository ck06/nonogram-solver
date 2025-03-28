<?php declare(strict_types=1);

namespace App\DTO;

use App\DTO\Values\CellValue;

/** Container DTO for the NonogramSolver */
class Solution
{
    public readonly CellValue $square;

    public function __construct(
        public readonly int $start,
        public readonly int $end,
        int|CellValue $value
    ) {
        $this->square = $value instanceof CellValue ? $value : CellValue::from($value);
    }
}
