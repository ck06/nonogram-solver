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
class StandaloneHintStrategy implements StrategyInterface
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
        $solutions = [];
        $gridSize = count($rowOrColumn->getData());
        $hints = $rowOrColumn->getHints();

        // on top of adding all the hint values together, we also have to add +1 for every hint past the first
        $totalHintedLength = array_sum($hints) + count($hints) - 1;

        // hint contains the entire row or column
        if ($totalHintedLength === $gridSize) {
            $pos = 0;
            foreach ($hints as $length) {
                $solutions[] = new Solution(++$pos, $pos + $length - 1, CellValue::SQUARE_FILLED);
                $pos += $length;

                // add an ignore after filling if this isn't the last solution
                if ($pos < $gridSize) {
                    $solutions[] = new Solution($pos, $pos, CellValue::SQUARE_IGNORED);
                }
            }
        }

        return $solutions;
    }
}
