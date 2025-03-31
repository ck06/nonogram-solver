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
 *     this repeats both from the leftmost square and the rightmost square, leaving a centered solution.  
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
        return [];
    }
}
