<?php declare(strict_types=1);

namespace App\DTO;

use App\DTO\Values\BoardOutput;
use App\DTO\Values\CellValue;

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
        $this->reset($this->height, $this->width);

        foreach ($array['data'] ?? [] as $rowNum => $row) {
            $characters = explode('', $row);
            foreach ($characters as $colNum => $character) {
                // map the inputs to our internal values
                $mappedCharacter = (match ((int)$character) {
                    0 => CellValue::SQUARE_OPEN,
                    1 => CellValue::SQUARE_FILLED,
                    2 => CellValue::SQUARE_IGNORED,
                })->value;

                $this->rows[$rowNum][$colNum] = $mappedCharacter;
            }
        }
    }

    public function isSolved(): bool
    {
        foreach ($this->getRows() as $row) {
            if (!$row->isSolved()) {
                return false;
            }
        }

        return true;
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

    /**
     * @return array<string, RowOrColumn>
     */
    public function getRows(): array
    {
        $rows = [];
        $rowCount = count($this->rows);
        for ($i = 1; $i <= $rowCount; $i++) {
            $rows[] = $this->getRow($i);
        }

        return $rows;
    }

    public function getRow(int|string $rowNum): RowOrColumn
    {
        return new RowOrColumn(
            $this->rows[(string)$rowNum],
            $this->rowHints[(string)$rowNum]
        );
    }

    /**
     * @return array<string, RowOrColumn>
     */
    public function getColumns(): array
    {
        $columns = [];
        $columnCount = count($this->columns);
        for ($i = 1; $i <= $columnCount; $i++) {
            $columns[] = $this->getColumn($i);
        }

        return $columns;
    }

    public function getColumn(int|string $colNum): RowOrColumn
    {
        return new RowOrColumn(
            $this->columns[(string)$colNum],
            $this->columnHints[(string)$colNum]
        );
    }

    public function getCell(int $row, int $col): CellValue
    {
        return CellValue::from($this->grid[$row][$col]);
    }

    public function updateCell(int $row, int $col, int|CellValue $newValue): self
    {
        $this->rows[$row][$col] = $newValue instanceof CellValue ? $newValue->value : $newValue;

        return $this;
    }

    public function updateMultipleInRow(int $row, int $fromCol, int $toCol, int|CellValue $newValue): self
    {
        for ($i = $fromCol; $i <= $toCol; $i++) {
            $this->updateCell($row, $i, $newValue);
        }

        return $this;
    }

    public function updateMultipleInColumn(int $col, int $fromRow, int $toRow, int|CellValue $newValue): self
    {
        for ($i = $fromRow; $i <= $toRow; $i++) {
            $this->updateCell($i, $col, $newValue);
        }

        return $this;
    }

    public function toJson(): string
    {
        $data = [
            'height' => $this->getHeight(),
            'width' => $this->getWidth(),
            'hints' => [
                'column' => $this->columnHints,
                'row' => $this->rowHints,
            ],
            'data' => array_map(
                static fn($row) => implode(
                    '',
                    array_map(static fn($cell) => match ($cell) {
                        CellValue::SQUARE_OPEN->value => 0,
                        CellValue::SQUARE_FILLED->value => 1,
                        CellValue::SQUARE_IGNORED->value => 2,
                    }, $row),
                ),
                $this->getGrid(),
            ),
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    public function draw(): string
    {
        return BoardOutput::drawBoard($this);
    }

    private function reset(?int $height, ?int $width): void
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
