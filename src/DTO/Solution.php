<?php declare(strict_types=1);

namespace App\DTO;

use App\DTO\Values\GridSquareValue;

/** Container DTO for the NonogramSolver */
class Solution
{
    public readonly GridSquareValue $square;

    public function __construct(
        public readonly int $start,
        public readonly int $end,
        int|GridSquareValue $value
    ) {
        $this->square = $value instanceof GridSquareValue ? $value : GridSquareValue::from($value);
    }
}
