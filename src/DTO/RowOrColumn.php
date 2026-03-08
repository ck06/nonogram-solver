<?php declare(strict_types=1);

namespace App\DTO;

use App\DTO\Values\CellValue;

class RowOrColumn
{
    private array $hints;
    private array $rowOrColumn = [];

    public function __construct(
        array $rowOrColumnData,
        string $hintsForRowOrColumn,
        private readonly Board $board,
        private readonly ?self $original = null
    ) {
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

    public function isOriginal(): bool {
        return $this->original === null;
    }

    public function getBoard(): Board
    {
        return $this->board;
    }

    public function getData(): array
    {
        return $this->rowOrColumn;
    }

    /**
     * Creates a copy of this row/column that's reversed (right-to-left or bottom-to-top)
     * @return RowOrColumn
     */
    public function flip(): RowOrColumn
    {
        if ($this->original !== null) {
            return $this->original;
        }

        return new self(
            array_reverse($this->getData()),
            strrev(implode(' ', $this->getHints())),
            $this->getBoard(),
            $this,
        );
    }

    public function getHints(): array
    {
        return array_map(static fn($hint) => (int)$hint, $this->hints);
    }
}
