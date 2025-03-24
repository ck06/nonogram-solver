<?php declare(strict_types=1);

namespace App\DTO;

use App\DTO\Values\GridSquareValue;

class Board
{
    private int $width;
    private int $height;
    private array $grid;
    private array $rows = [];
    private array $columns = [];
    private array $rowHints;
    private array $columnHints;

    public function __construct(string $dataFromController)
    {
        $array = json_decode($dataFromController, true, 512, JSON_THROW_ON_ERROR);
        $this->columnHints = $array['hints']['column'];
        $this->rowHints = $array['hints']['row'];
        $this->height = $array['height'];
        $this->width = $array['width'];
        $this->resetGrid($this->height, $this->width);

        foreach ($array['data'] ?? [] as $rowNum => $row) {
            $characters = explode('', $row);
            foreach ($characters as $colNum => $character) {
                $this->rows[$rowNum][$colNum] = (int)$character;
            }
        }
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getGrid(): array
    {
        return $this->grid;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getRowHints(): array
    {
        return $this->rowHints;
    }

    public function getColumnHints(): array
    {
        return $this->columnHints;
    }

    public function updateSquare(int $row, int $col, int|GridSquareValue $newValue): self
    {
        $this->rows[$row][$col] = $newValue instanceof GridSquareValue ? $newValue->value : $newValue;

        return $this;
    }

    public function updateMultipleInRow(int $row, int $fromCol, int $toCol, int|GridSquareValue $newValue): self
    {
        for ($i = $fromCol; $i <= $toCol; $i++) {
            $this->updateSquare($row, $i, $newValue);
        }

        return $this;
    }

    public function updateMultipleInColumn(int $col, int $fromRow, int $toRow, int|GridSquareValue $newValue): self
    {
        for ($i = $fromRow; $i <= $toRow; $i++) {
            $this->updateSquare($i, $col, $newValue);
        }

        return $this;
    }

    public function toJson(): string
    {
        $data = [
            'height' => $this->getHeight(),
            'width' => $this->getWidth(),
            'hints' => [
                'column' => $this->getColumnHints(),
                'row' => $this->getRowHints(),
            ],
            'data' => array_map(static fn($row) => implode('', $row), $this->getGrid()),
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    private function resetGrid(?int $height, ?int $width): void
    {
        $height = $height ?? $this->height;
        $width = $width ?? $this->width;
        $this->rows = [];
        $this->columns = [];

        // Starting arrays at 1 so that we can use a "row-col" grid notation.
        // As an example, let's take a 10x10 grid. The four corners in this grit will be notated as
        // 1-1 for top left, 1-10 for top right, 10-1 for bottom left and 10-10 for bottom right.
        for ($i = 1; $i <= $height; $i++) {
            $this->grid[$i] = [];
            $this->rows[$i] = [];

            for ($j = 1; $j <= $width; $j++) {
                $this->grid[$i][$j] = 0;

                // explicitly set these to null first so the keys exist when turning them into pointers
                $this->rows[$i][$j] = null;
                $this->rows[$i][$j] =& $this->grid[$i][$j];

                $this->columns[$j][$i] = null;
                $this->columns[$j][$i] =& $this->grid[$i][$j];
            }
        }
    }
}
