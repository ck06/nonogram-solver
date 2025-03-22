<?php declare(strict_types=1);

namespace App\DTO;

class Board
{
    private int $width;
    private int $height;
    private array $data;
    private array $rows = [];
    private array $columns = [];
    private array $rowHints;
    private array $columnHints;

    /**
     * Example of expected input JSON
     * {
     *     'height': 10,
     *     'width': 10,
     *     'hints': {
     *         'column': ['6', '3 2', '1 3', '4', '2', '1 4', '2 2', '4', '2 2', '5'],
     *         'row': ['4 1 3', '2 2 4', '5 2 1', '1 2 3', '3 2', '2', '2', '2', '1', '1']
     *     },
     *     'data': [                // 0 = open, 1 = filled in, 2 = crossed out
     *         '1111200000',
     *         '1121100000',
     *         '1111100000',
     *         '1211200000',
     *         '1112200000',
     *         '1122222222',
     *         '2222211222',
     *         '2222211222',
     *         '2222212222',
     *         '2222212222'
     *     ]
     * }
     */
    public function __construct(string $dataFromController)
    {
        $array = json_decode($dataFromController, true, 512, JSON_THROW_ON_ERROR);
        $this->columnHints = $array['hints']['column'];
        $this->rowHints = $array['hints']['row'];
        $this->height = $array['height'];
        $this->width = $array['width'];
        $this->data = $array['data'] ?? [];
        if (!$this->data || $this->data === []) {
            $this->resetGrid();
        }

        foreach ($this->data as $rowNum => $row) {
            $this->rows[] = $row;
            foreach (explode('', $row) as $colNum => $character) {
                if (!isset($this->columns[$colNum])) {
                    $this->columns[$colNum] = str_repeat('0', $this->height);
                }

                $this->columns[$colNum][$rowNum] = $character;
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

    public function getData(): array
    {
        return $this->data;
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

    public function toJson(): string
    {
        $data = [
            'height' => $this->getHeight(),
            'width' => $this->getWidth(),
            'hints' => [
                'column' => $this->getColumnHints(),
                'row' => $this->getRowHints(),
            ],
            'data' => $this->getRows(),
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    private function resetGrid(): void
    {
        for ($i = 0; $i < $this->height; $i++) {
            $this->data[$i] = str_repeat('0', $this->width);
        }
    }
}
