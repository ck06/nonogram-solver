<?php declare(strict_types=1);

namespace App\Service;

use App\DTO\Board;
use App\DTO\RowOrColumn;
use App\Service\NonogramSolverStrategy\StrategyCollection;
use App\Service\NonogramSolverStrategy\StrategyOpenerCollection;

class NonogramSolver
{
    private Board $board;
    private bool $triedOneTimeStrategies = false;

    public function __construct(
        private StrategyCollection $strategies,
        private StrategyOpenerCollection $oneTimeStrategies,
    ) {
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
        // some strategies only need to be checked once. these will be
        // handled separately to avoid excess calls in very big grids.
        // additionally, they MUST be done first as other strategies
        // may rely on the results of these one time strategies.
        if (!$this->triedOneTimeStrategies) {
            $this->tryOneTimeStrategies();
            $this->triedOneTimeStrategies = true;
            dump("Board after applying one-time strategies: ", $this->board->drawWithHints());
        }

        $board = $this->board->drawWithHints();
        foreach ([$this->board->getRows(), $this->board->getColumns()] as $rowsOrColumns) {
            /** @var RowOrColumn $rowOrColumn */
            foreach ($rowsOrColumns as $rowOrColumn) {
                $solutions = $this->strategies->tryToSolve($rowOrColumn);
                $this->applySolutions($rowOrColumn, $solutions);

                # TODO - for debugging purposes, remove later
                if ($board !== $this->board->drawWithHints()) {
                    $board = $this->board->drawWithHints();
                    dump($this->board->drawWithHints());
                }
            }
        }
    }

    private function tryOneTimeStrategies(): void
    {
        foreach ([$this->board->getRows(), $this->board->getColumns()] as $rowsOrColumns) {
            /** @var RowOrColumn $rowOrColumn */
            foreach ($rowsOrColumns as $rowOrColumn) {
                $solutions = $this->oneTimeStrategies->trytoSolve($rowOrColumn);
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
    }
}
