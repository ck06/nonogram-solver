<?php declare(strict_types=1);

namespace App\Service\NonogramSolverStrategy;

use App\DTO\RowOrColumn;
use App\DTO\Solution;
use App\DTO\Values\CellValue;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * This strategy looks at the solved cells and the associated hints to determine if they should connect.
 * examples:
 *   (size: 10, hint: '5')   [ ][*][ ][ ][*][ ][ ][ ][ ][ ] => [ ][*][*][*][*][ ][ ][ ][ ][ ]
 *   (size: 10, hint: '1 5') [ ][*][ ][*][ ][*][ ][*][ ][ ] => [ ][*][ ][*][*][*][*][*][ ][ ]
 */
class FillTheGapStrategy implements StrategyInterface
{
    public function supports($object, array $options): bool
    {
        if (!$object instanceof RowOrColumn) {
            return false;
        }

        if ($object->isSolved()) {
            return false;
        }

        // due to large overlap with other strategies on small rows, a minimum size has been configured
        if (count($object->getData()) <= 10) {
            return false;
        }

        return true;
    }

    public function tryToSolve(RowOrColumn $rowOrColumn, array $options = []): array
    {
        return [];
    }
}
