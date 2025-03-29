<?php declare(strict_types=1);

namespace App\Service\NonogramSolverStrategy;

use App\DTO\RowOrColumn;
use App\DTO\Solution;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class StrategyCollection
{
    public const TAG_NAME = 'nonogramSolverStrategy';

    public function __construct(#[AutowireIterator(self::TAG_NAME)] private readonly iterable $strategies)
    {
    }

    /**
     * @param array<string,mixed> $options
     * @return array<Solution>
     */
    public function tryToSolve(RowOrColumn $rowOrColumn, array $options = []): array
    {
        if ($rowOrColumn->isSolved()) {
            return [];
        }

        /** @var StrategyInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if (!$strategy->supports($rowOrColumn, $options)) {
                continue;
            }

            $solutions = $strategy->tryToSolve($rowOrColumn, $options);
            if ($solutions !== []) {
                return $solutions;
            }
        }

        return [];
    }
}