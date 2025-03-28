<?php declare(strict_types=1);

namespace App\Service;

use App\DTO\Board;
use App\DTO\RowOrColumn;
use App\DTO\Solution;
use App\DTO\Values\CellValue;
use App\Service\NonogramSolverStrategy\StrategyCollection;

class NonogramSolver
{
    private Board $board;

    public function __construct(private StrategyCollection $strategies)
    {
    }

    public function solve(Board $board): Board
    {
        $this->board = $board;

        $lastSolution = null;
        while ($this->board->isSolved() === false) {
            // additional exit condition - board may not be solved but repeated attempts do not result in progress
            if ($lastSolution !== null && $lastSolution === $this->board->draw()) {
                break;
            }

            $lastSolution = $this->board->draw();

            $this->tryToSolve();
        }

        return $this->board;
    }

    private function tryToSolve(): void
    {
        foreach ([$this->board->getRows(), $this->board->getColumns()] as $rowsOrColumns) {
            /** @var RowOrColumn $rowOrColumn */
            foreach ($rowsOrColumns as $rowOrColumn) {
                $solutions = $this->strategies->tryToSolve($rowOrColumn);
                $this->applySolutions($rowOrColumn, $solutions);
            }
        }
    }

    private function applySolutions(RowOrColumn $rowOrColumn, array $solutions): void
    {
        if ($solutions === []) {
            return;
        }

        foreach ($solutions as $solution) {
            for ($i = $solution->start; $i <= $solution->end; $i++) {
                $square = &$rowOrColumn->getData()[(string)$i];
                $square = $solution->square->value;
            }
        }

        dump($this->board->draw());
    }

    //////////////// TODO: convert all methods below here to strategies

    /**
     * @return array<Solution>
     */
    private function solveGuaranteedAfterRemovingIgnored(array $rowOrColumn, array $hints): array
    {
        $solutions = [];

        $filteredRowOrColumn = [];
        foreach ($rowOrColumn as $pos => $square) {
            if (CellValue::from($square) === CellValue::SQUARE_IGNORED) {
                continue;
            }

            $filteredRowOrColumn[$pos] = $square;
        }

        $gridSize = count($filteredRowOrColumn);

        // TODO: check if the hints solve the row or column after removing ignored cells
        // $openSequences = $this->readRowOrColumn($rowOrColumn, $hints);
        return [];
    }

    private function readRowOrColumn(array $rowOrColumn, ?array $hints = null): array
    {
        $openSequences = [];
        $sequenceStart = null;

        foreach ($rowOrColumn as $pos => $value) {
            $value = CellValue::from($value);

            // finish any ongoing sequences if we hit a guaranteed ignore
            if ($sequenceStart !== null && $value === CellValue::SQUARE_IGNORED) {
                $openSequences[$sequenceStart] = $pos - $sequenceStart;
                $sequenceStart = null;
            }

            // start a sequence if we find our first open or filled in square
            if ($sequenceStart === null && $value !== CellValue::SQUARE_IGNORED) {
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
