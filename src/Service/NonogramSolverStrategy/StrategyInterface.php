<?php declare(strict_types=1);

namespace App\Service\NonogramSolverStrategy;

use App\DTO\RowOrColumn;
use App\DTO\Solution;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(StrategyCollection::TAG_NAME)]
interface StrategyInterface
{
    public function supports($object, array $options): bool;

    /**
     * @return array<Solution>
     */
    public function tryToSolve(RowOrColumn $rowOrColumn, array $options): array;
}
