<?php declare(strict_types=1);

namespace App\Service\NonogramSolverStrategy;

use App\DTO\RowOrColumn;
use App\DTO\Solution;
use App\DTO\Values\CellValue;
use Exception;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * This strategy looks at the solved cells and the associated hints to determine if the row is completed.
 * If all the required cells are found, it will fill the remaining squares with X's
 * If an error is found, it will immediately throw an exception.
 * Error states:
 *   - enough squares filled in, but does not satisfy hint requirements.
 *   - not enough squares are filled in but entire row is filled in
 *   - too many squares filled in
 * examples:
 *   (size: 5, hint: '2')    [ ][*][*][ ][ ] => [X][*][*][X][X]  => completed; fills the rest with X's
 *   (size: 5, hint: '1')    [*][X][X][X][X] => [*][X][X][X][X]  => completed; nothing to do
 *   (size: 5, hint: '2')    [ ][*][*][*][ ] => ERROR            => too many squares
 *   (size: 5, hint: '2')    [ ][*][ ][*][ ] => ERROR            => squares in impossible formation
 *   (size: 5, hint: '1 1')  [*][ ][*][ ][*] => ERROR            => both of the above at once
 *   (size: 5, hint: '2')    [*][X][X][X][X] => ERROR            => not enough squares, but row filled
 */
#[AsTaggedItem(StrategyCollection::TAG_NAME, -100)]
class CheckCompletedStrategy implements StrategyInterface
{
    public function supports($object, array $options): bool
    {
        return $object instanceof RowOrColumn;
    }

    public function tryToSolve(RowOrColumn $rowOrColumn, array $options = []): array
    {
        $filledSquares = array_filter(
            $rowOrColumn->getData(),
            static fn(int $roc) => $roc === CellValue::SQUARE_FILLED->value,
        );

        $ignoredSquares = array_filter(
            $rowOrColumn->getData(),
            static fn(int $roc) => $roc === CellValue::SQUARE_IGNORED->value,
        );

        if (count($filledSquares) + count($ignoredSquares) === count($rowOrColumn->getData())) {
            // if all squares are filled/ignored, but the hint isn't matched, throw an error
            if (count($filledSquares) !== array_sum($rowOrColumn->getHints())) {
                $this->error($rowOrColumn, 'Row is completed, but does not match the hints.');
            }

            // if all squares are filled/ignored, and it matches the hints, there's nothing to do.
            return $this->doNothing();
        }

        // if not enough squares are filled in, we can't determine completion.
        if (count($filledSquares) < array_sum($rowOrColumn->getHints())) {
            return $this->doNothing();
        }

        // if too many squares are filled in, we know for sure something is wrong.
        if (count($filledSquares) > array_sum($rowOrColumn->getHints())) {
            $this->error($rowOrColumn, 'Too many squares filled in');
        }

        return $this->tryToSolveSequence($rowOrColumn);
    }

    private function tryToSolveSequence(RowOrColumn $rowOrColumn): array
    {
        // TODO: check if hints are still possible on the given grid
        // TODO: fill remainder with X's if we have exactly enough squares filled in and they match the hints
        return $this->doNothing();

        // return $this->complete($rowOrColumn);
    }

    private function doNothing(): array
    {
        return [];
    }

    private function error(RowOrColumn $rowOrColumn, ?string $additionalContext = ''): void
    {
        throw new RuntimeException("
            Something went wrong while solving this board.
            Context: $additionalContext
            Check the state below to verify what's going on.
            
            " . $rowOrColumn->getBoard()->drawWithHints());
    }

    private function complete(RowOrColumn $rowOrColumn): array
    {
        $solutions = [];
        foreach ($rowOrColumn->getData() as $cellNum => $cell) {
            if ($cell === CellValue::SQUARE_OPEN->value) {
                $solutions[] = new Solution($cellNum, $cellNum, CellValue::SQUARE_IGNORED);
            }
        }

        return $solutions;
    }
}
