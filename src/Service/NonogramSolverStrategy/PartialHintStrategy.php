<?php declare(strict_types=1);

namespace App\Service\NonogramSolverStrategy;

use App\DTO\RowOrColumn;
use App\DTO\Solution;
use App\DTO\Values\CellValue;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * This strategy only looks at the associated hint and, if that hint matches the length exactly, fills it in.
 * It does not look at the contents of the row/column at all, that responsibility lies with another strategy.
 */
#[AsTaggedItem(StrategyCollection::TAG_NAME, 100)]
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
