<?php declare(strict_types=1);

namespace App\DTO;

use App\DTO\Values\CellValue;

class RowOrColumn
{
    private array $hints;
    private array $rowOrColumn = [];

    public function __construct(array $rowOrColumnData, string $hintsForRowOrColumn)
    {
        foreach ($rowOrColumnData as $pos => &$square) {
            $this->rowOrColumn[$pos] = &$square;
        }

        unset($square);

        $this->hints = explode(' ', $hintsForRowOrColumn);
    }

    public function isSolved(): bool
    {
        return !in_array(CellValue::SQUARE_OPEN->value, $this->rowOrColumn, true);
    }

    public function isBlank(): bool
    {
        return !in_array(CellValue::SQUARE_FILLED->value, $this->rowOrColumn, true)
            && !in_array(CellValue::SQUARE_IGNORED->value, $this->rowOrColumn, true);
    }

    public function markAsSolved(): self
    {
        foreach ($this->rowOrColumn as &$square) {
            if ($square === CellValue::SQUARE_OPEN->value) {
                $square = CellValue::SQUARE_IGNORED->value;
            }
        }

        return $this;
    }

    public function getData(): array
    {
        return $this->rowOrColumn;
    }

    public function getHints(): array
    {
        return array_map(static fn($hint) => (int)$hint, $this->hints);
    }
}
