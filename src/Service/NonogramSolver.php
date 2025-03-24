<?php declare(strict_types=1);

namespace App\Service;

use App\DTO\Board;
use App\DTO\Solution;
use App\DTO\Values\GridSquareValue;

class NonogramSolver
{
    private Board $board;

    public function __construct()
    {
    }

    public function solve(Board $board): Board
    {
        $this->board = $board;

        $this->solveGuarantees();

        return $this->board;
    }

    /**
     * "Guarantees" in this case refer to any row or column that can be [partially] solved on its own.
     * This includes stuff like...
     * - singular or multiple hints that add up to the entire grid's height or width, instantly solving them
     * - singular hints that are larger than half the grid's height or width, partially solving them
     * - multiple hints that, when using them to ignore parts of the grid, lead to the above use case
     */
    private function solveGuarantees(): void
    {
        foreach ($this->board->getRows() as $rowNum => $row) {
            $hints = explode(' ', $this->board->getRowHints()[$rowNum]);
            $hints = array_map(static fn($hint) => (int)$hint, $hints);

            // this one includes any form of hints that solve the whole row or column with no other input needed.
            $solutions = $this->solveGuaranteedRowOrColumnByHintAlone($hints, $this->board->getWidth());
            if ($solutions !== []) {
                // todo apply solution
                dd('solutions found!', $solutions);
            }

            // todo finish method
            $solutions = $this->solveGuaranteedRowOrColumnAfterRemovingIgnoredSquares($row, $hints);
        }

        // todo process columns
    }

    /**
     * @return array<Solution>
     */
    private function solveGuaranteedRowOrColumnByHintAlone(array $hints, int $gridSize): array
    {
        $solutions = [];

        // on top of adding all the hint values together, we also have to add +1 for every hint past the first
        $totalHintedLength = array_sum($hints) + count($hints) - 1;

        // hint contains the entire row or column
        if ($totalHintedLength === $gridSize) {
            $pos = 0;
            foreach ($hints as $length) {
                $solutions[] = new Solution(++$pos, $pos + $length - 1, GridSquareValue::SQUARE_FILLED);
                $pos += $length;

                // add an ignore after filling if this isn't the last solution
                if ($pos < $gridSize) {
                    $solutions[] = new Solution($pos, $pos, GridSquareValue::SQUARE_IGNORED);
                }
            }

            return $solutions;
        }

        return $solutions;
    }

    /**
     * @return array<Solution>
     */
    private function solveGuaranteedRowOrColumnAfterRemovingIgnoredSquares(array $rowOrColumn, array $hints): array
    {
        // TODO: check if the hints solve the row or column after removing ignored cells
        // $openSequences = $this->readRowOrColumn($rowOrColumn, $hints);
        return [];
    }

    private function readRowOrColumn(array $rowOrColumn, ?array $hints = null): array
    {
        $openSequences = [];
        $sequenceStart = null;

        foreach ($rowOrColumn as $pos => $value) {
            $value = GridSquareValue::from($value);

            // finish any ongoing sequences if we hit a guaranteed ignore
            if ($sequenceStart !== null && $value === GridSquareValue::SQUARE_IGNORED) {
                $openSequences[$sequenceStart] = $pos - $sequenceStart;
                $sequenceStart = null;
            }

            // start a sequence if we find our first open or filled in square
            if ($sequenceStart === null && $value !== GridSquareValue::SQUARE_IGNORED) {
                $sequenceStart = $pos;
            }
        }

        // make sure to add the last sequence if the last square was open
        if ($sequenceStart !== null) {
            $openSequences[$sequenceStart] = count($rowOrColumn) - $sequenceStart;
        }

        // if hints are given, filter out any sequences that are too small for the smallest hint to fit.
        if ($hints !== null) {
            $smallestHint = min($hints);
            foreach ($openSequences as $start => $length) {
                if ($length < $smallestHint) {
                    unset($openSequences[$start]);
                }
            }
        }

        return $openSequences;
    }
}
