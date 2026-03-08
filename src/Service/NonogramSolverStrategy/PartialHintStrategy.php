<?php declare(strict_types=1);

namespace App\Service\NonogramSolverStrategy;

use App\DTO\RowOrColumn;
use App\DTO\Solution;
use App\DTO\Values\CellValue;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * This strategy looks at the associated hint(s) and fills in the slots that it can guarantee are included.
 * This uses several different steps based on the status of the row;
 *  1. if there is a single hint, and that hint is greater than half of the row's length (rounded up),
 *     it works by counting the amount of cells until the halfway point and filling in from there.
 *     examples:
 *       (size: 5, hint: '3') [ ][ ][*][ ][ ]
 *       (size: 5, hint: '4') [ ][*][*][*][ ]
 *  2. if there are multiple hints, it does the above, but offsets the starting points by the other hints
 *     examples:
 *       (size: 7, hint: '2 3') [ ][*][ ][ ][*][*][ ]
 *  3. if the row comes partially filled...
 *     - a filled cell will be used as the solution's starting point
 *     - ignored cells on the edges will be removed from the row size
 *     - solved hints will be interpreted as ignored cells
 *     - unreachable cells will be solved as new ignored cells
 *     examples:
 *       (size: 6, hint: '3', prefilled)   [ ][*][ ][ ][ ][ ] => [ ][*][*][ ][X][X]
 *       (size: 6, hint: '1 2', prefilled) [ ][*][ ][ ][ ][ ] => [X][*][X][ ][*][ ]
 */
#[AsTaggedItem(StrategyCollection::TAG_NAME)]
class PartialHintStrategy implements StrategyInterface
{
    public function supports($object, array $options): bool
    {
        if (!$object instanceof RowOrColumn) {
            return false;
        }

        if ($object->isSolved()) {
            return false;
        }

        return true;
    }

    public function tryToSolve(RowOrColumn $rowOrColumn, array $options = []): array
    {
        $foundSolutions = match (count($rowOrColumn->getHints())) {
            0 => [],
            1 => $this->solveSingleHint($rowOrColumn, $rowOrColumn->getHints()[0]),
            default => $this->solveMultiHint($rowOrColumn)
        };

        $solutions = [];
        foreach ($foundSolutions as $solution) {
            $solutions[] = $solution;
        }

        return $solutions;
    }

    private function solveMultiHint(RowOrColumn $rowOrColumn): array
    {
        // TODO - does not work as intended, but probably caused by solveSingleHint
        //        issues. check back after fixing the strategy-specific test.

        $solutions = [];
        $hints = $rowOrColumn->getHints();
        foreach ($hints as $num => $hint) {
            [$leftOffset, $rightOffset] = $this->getOffsetForHintNumber($rowOrColumn, $num);
            foreach($this->solveSingleHint($rowOrColumn, $hint, $leftOffset, $rightOffset) as $solution) {
                $solutions[] = $solution;
            }
        }

        return $solutions;
    }

    private function solveSingleHint(
        RowOrColumn $rowOrColumn,
        int $hint,
        int $extraLeftOffset = 0,
        int $extraRightOffset = 0
    ): array {
        $leftOffset = $this->getLeftOffset($rowOrColumn) + $extraLeftOffset;
        $rightOffset = $this->getRightOffset($rowOrColumn) + $extraRightOffset;
        $openSize = count($rowOrColumn->getData()) - $leftOffset - $rightOffset;
        if ((int)ceil($openSize / 2) > $hint) {
            // even with ignored squares removed, the hint is smaller than half the remaining squares.
            return [];
        }

        $start = (int)ceil($openSize / 2);
        $end = $hint - (int)floor($openSize / 2);

        return [
            new Solution($start - $end, $start + $end, CellValue::SQUARE_FILLED),
        ];
    }

    private function getLeftOffset(RowOrColumn $rowOrColumn): int
    {
        $offset = 0;
        $end = count($rowOrColumn->getData());
        for ($i = 1; $i <= $end; $i++) {
            $square = $rowOrColumn->getData()[$i];
            if ($square !== CellValue::SQUARE_IGNORED) {
                break;
            }

            $offset++;
        }

        return $offset;
    }

    private function getRightOffset(RowOrColumn $rowOrColumn): int
    {
        $offset = 0;
        $start = count($rowOrColumn->getData());
        for ($i = $start; $i > 0; $i--) {
            $square = $rowOrColumn->getData()[$i];
            if ($square !== CellValue::SQUARE_IGNORED) {
                break;
            }

            $offset++;
        }

        return $offset;
    }

    /**
     * @return array<int> containing leftOffset and rightOffset, in that order.
     */
    private function getOffsetForHintNumber(RowOrColumn $rowOrColumn, int $hintNum): array
    {
        /*
         * offset math explained:
         *   - we add up all the hint values together, then remove the current hint's value from it
         *   - we add +1 for every hint added together to reflect nonograms' mandatory padding
         *     (this includes the padding for the hint we're getting the offset for)
         *   - we then subtract the value of our own hint, as we want to find those squares.
        */
        $hints = $rowOrColumn->getHints();
        $count = count($hints);
        $sum = array_sum($hints);

        $totalOffset = $sum + ($count) - $hints[$hintNum];
        if ($hintNum === 0) {
            return [0, $totalOffset];
        }

        if ($hintNum === $count - 1) {
            return [$totalOffset, 0];
        }

        $leftOffset = 0;
        $rightOffset = 0;
        foreach ($hints as $num => $hint) {
            $counter =& $hintNum > $num ? $leftOffset : $rightOffset;
            if ($hintNum === $num) {
                continue;
            }

            $counter += $hint + 1;
        }

        return [$leftOffset, $totalOffset];
    }
}
