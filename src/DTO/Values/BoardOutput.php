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

    public static function drawBoard(Board $board, bool $includeHints = false): string
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

        if ($includeHints) {
            return self::drawHints($board, $canvas);
        }

        return $canvas;
    }

    # in a typical nonogram layout, the hints are...
    # row => to the left
    # col => at the top
    #
    # for this output we will assume a max width of 2-digit hints for the columns,
    # leaving the first char blank if <10
    public static function drawHints(Board $board, string $canvas): string
    {
        $rows = [];
        $padding = 0;
        foreach ($board->getRows() as $row) {
            $rowHints = implode(' ', $row->getHints());
            $padding = max($padding, strlen($rowHints));
            $rows[] = $rowHints;
        }

        $maxColHints = 0;
        foreach ($board->getColumns() as $column) {
            $maxColHints = max(count($column->getHints()), $maxColHints);
        }

        $columnCanvas = array_fill(0, $maxColHints, '');
        foreach ($board->getColumns() as $column) {
            $hints = $column->getHints();
            $offset = $maxColHints - count($hints);
            for ($i = 0; $i < $maxColHints; $i++) {
                $hintString = str_repeat(' ', 6);
                if ($i >= $offset) {
                    // we want our hint to be in the middle of the colum
                    // since we include the left but exclude the right separator,
                    // this means we'll have 2-3 spaces in front and 2 spaces behind.
                    $hint = $hints[$i - $offset];
                    $hintString = sprintf(
                        '%s%d%s',
                        $hint < 10 ? '   ' : '  ',
                        $hint,
                        '  ',
                    );
                }
                $columnCanvas[$i] .= $hintString;
            }
        }

        // now to put it all together
        $hintedCanvas = '';
        foreach ($columnCanvas as $columnRow) {
            // $padding contains the max length of the row hints
            // on top of this, we need 1 whitespace in either direction.
            $hintedCanvas .= str_repeat(' ', $padding+2) . $columnRow . PHP_EOL;
        }

        $originalRows = explode(PHP_EOL, rtrim($canvas));
        foreach ($originalRows as $i => $originalRow) {
            // hints are on even rows - odd rows are separators.
            // we -1 the $i to remove fuckiness with our modulo
            $hint = str_repeat(' ', $padding);
            if (--$i % 2 === 0) {
                $hint = $rows[$i/2];
                $hint = str_pad($hint, $padding, ' ', STR_PAD_LEFT);
            }

            $hintedCanvas .= " $hint " . trim($originalRow) . PHP_EOL;
        }

        return $hintedCanvas;
    }
}
