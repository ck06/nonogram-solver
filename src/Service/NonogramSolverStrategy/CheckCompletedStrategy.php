<?php declare(strict_types=1);

namespace App\Service\NonogramSolverStrategy;

use App\DTO\RowOrColumn;
use App\DTO\Solution;
use App\DTO\Values\CellValue;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * This strategy looks at the solved cells and the associated hints to determine if the row is completed.
 * If an error is found (i.e. too many solved squares), it resets the data and sets a flag on the object.
 * If an error is found and the flag is already set, it will elevate to an exception.
 * If a row/column is already flagged as completed, this strategy will continuously verify it's correct.
 * examples:
 *   (size: 5, hint: '2', prefilled)   [ ][*][*][ ][ ] => [X][*][*][X][X]
 *   (size: 5, hint: '2', prefilled)   [ ][*][*][*][ ] => [ ][ ][ ][ ][ ]
 *   (size: 5, hint: '2', prefilled)   [ ][*][ ][*][ ] => [ ][ ][ ][ ][ ]
 *   (size: 5, hint: '1 1', prefilled) [*][ ][*][ ][*] => [ ][ ][ ][ ][ ]
 *   (size: 5, hint: '1', prefilled)   [*][X][X][X][X] => [*][X][X][X][X]
 *   (size: 5, hint: '1', prefilled)   [*][*][X][X][X] => [ ][ ][ ][ ][ ]
 */
#[AsTaggedItem(StrategyCollection::TAG_NAME, -100)]
class CheckCompletedStrategy implements StrategyInterface
{
    public function supports($object, array $options): bool
    {
        if (!$object instanceof RowOrColumn) {
            return false;
        }

        // this strategy will continually verify existing solved state
        return true;
    }

    public function tryToSolve(RowOrColumn $rowOrColumn, array $options = []): array
    {
        return [];
    }
}
