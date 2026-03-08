<?php declare(strict_types=1);

namespace App\DTO;

use App\DTO\Values\CellValue;

class RowOrColumn
{
    private Board $board;
    private array $hints;
    private array $rowOrColumn = [];
    private array $warnings = [];

    public function __construct(array $rowOrColumnData, string $hintsForRowOrColumn, ?Board $board)
    {
        foreach ($rowOrColumnData as $pos => &$square) {
            $this->rowOrColumn[$pos] = &$square;
        }

        unset($square);

        $this->hints = explode(' ', $hintsForRowOrColumn);
        $this->board = $board;
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

    public function getBoard(): Board
    {
        return $this->board;
    }

    public function getData(): array
    {
        return $this->rowOrColumn;
    }

    public function getHints(): array
    {
        return array_map(static fn($hint) => (int)$hint, $this->hints);
    }

    public function hasWarning(string $warning): bool
    {
        return in_array($warning, $this->warnings, true);
    }

    public function addWarning(string $warning): self
    {
        $this->warnings[$warning] = $warning;

        return $this;
    }
}
