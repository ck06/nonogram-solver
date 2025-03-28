<?php declare(strict_types=1);

namespace App\DTO\Values;

use App\DTO\Board;

enum BoardOutput: string
{
    case OUTPUT_BORDER_VERTICAL = '  │  ';
    case OUTPUT_BORDER_HORIZONTAL = '―――――';
    case OUTPUT_BORDER_TOP_DIVIDER = '┬';
    case OUTPUT_BORDER_LEFT_DIVIDER = '  ├';
    case OUTPUT_BORDER_RIGHT_DIVIDER = '┤  ';
    case OUTPUT_BORDER_BOTTOM_DIVIDER = '┴';
    case OUTPUT_BORDER_CORNER_TOP_LEFT = '  ┌';
    case OUTPUT_BORDER_CORNER_TOP_RIGHT = '┐  ';
    case OUTPUT_BORDER_CORNER_BOTTOM_LEFT = '  └';
    case OUTPUT_BORDER_CORNER_BOTTOM_RIGHT = '┘  ';
    case OUTPUT_BORDER_CENTER = '┼';
    case OUTPUT_ERROR = '';

    public static function drawBoard(Board $board): string
    {
        $width = 2 * $board->getWidth() + 1;
        $height = 2 * $board->getHeight() + 1;
        $canvas = '';

        for ($row = 1; $row <= $height; $row++) {
            for ($col = 1; $col <= $width; $col++) {
                $firstRow = $row === 1;
                $firstCol = $col === 1;
                $lastRow = $row === $height;
                $lastCol = $col === $width;
                $evenRow = $row % 2 === 0;
                $evenCol = $col % 2 === 0;
                $oddRow = !$evenRow;
                $oddCol = !$evenCol;

                // if both values are even, we divide both by 2 and output that grid value
                if ($evenRow && $evenCol) {
                    $canvas .= CellOutput::fromGridSquareValue(
                        $board->getCell((int)($row / 2), (int)($col / 2))
                    )->value;
                }

                // any other combination of values indicates a separator
                $canvas .= (match (true) {
                    $lastRow && $lastCol => self::OUTPUT_BORDER_CORNER_BOTTOM_RIGHT,
                    $lastRow && $firstCol => self::OUTPUT_BORDER_CORNER_BOTTOM_LEFT,
                    $firstRow && $lastCol => self::OUTPUT_BORDER_CORNER_TOP_RIGHT,
                    $firstRow && $firstCol => self::OUTPUT_BORDER_CORNER_TOP_LEFT,
                    $lastRow && $oddCol => self::OUTPUT_BORDER_BOTTOM_DIVIDER,
                    $oddRow && $lastCol => self::OUTPUT_BORDER_RIGHT_DIVIDER,
                    $oddRow && $firstCol => self::OUTPUT_BORDER_LEFT_DIVIDER,
                    $firstRow && $oddCol => self::OUTPUT_BORDER_TOP_DIVIDER,
                    $oddRow && $evenCol => self::OUTPUT_BORDER_HORIZONTAL,
                    $evenRow && $oddCol => self::OUTPUT_BORDER_VERTICAL,
                    $oddRow && $oddCol => self::OUTPUT_BORDER_CENTER,
                    default => self::OUTPUT_ERROR,
                })->value;
            }

            $canvas .= PHP_EOL;
        }

        return $canvas;
    }
}
