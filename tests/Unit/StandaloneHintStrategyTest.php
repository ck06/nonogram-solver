<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\Solution;
use App\DTO\Values\CellValue;
use App\Service\NonogramSolverStrategy\StandaloneHintStrategy;
use PHPUnit\Framework\Attributes\DataProvider;

class StandaloneHintStrategyTest extends RowOrColumnTestCase
{
    #[DataProvider('StandaloneHintStrategyDataProvider')]
    public function testStandaloneHintStrategy($input, $expected): void
    {
        $strategy = new StandaloneHintStrategy();
        $actual = $strategy->tryToSolve($input);

        self::assertEquals($expected, $actual);
    }

    public static function StandaloneHintStrategyDataProvider(): array
    {
        return [
            [
                // unable to solve
                'input' => self::createBlankRowOrColumn(5, '1 2'),
                'expected' => [],
            ],
            [
                // strategy should ignore any prefilled data, and still come up with no solution
                'input' => self::createRowOrColumn([1, 2, 0, 1, 2], '1 2'),
                'expected' => [],
            ],
            [
                'input' => self::createBlankRowOrColumn(5, '5'),
                'expected' => [
                    new Solution(1, 5, CellValue::SQUARE_FILLED)
                ],
            ],
            [
                'input' => self::createBlankRowOrColumn(5, '3 1'),
                'expected' => [
                    new Solution(1, 3, CellValue::SQUARE_FILLED),
                    new Solution(4, 4, CellValue::SQUARE_IGNORED),
                    new Solution(5, 5, CellValue::SQUARE_FILLED),
                ],
            ],
            [
                // strategy should ignore any prefilled data and is leading - meaning it will overwrite it
                'input' => self::createRowOrColumn([2, 2, 1, 1, 0], '3 1'),
                'expected' => [
                    new Solution(1, 3, CellValue::SQUARE_FILLED),
                    new Solution(4, 4, CellValue::SQUARE_IGNORED),
                    new Solution(5, 5, CellValue::SQUARE_FILLED),
                ],
            ],
            [
                'input' => self::createBlankRowOrColumn(5, '1 1 1'),
                'expected' => [
                    new Solution(1, 1, CellValue::SQUARE_FILLED),
                    new Solution(2, 2, CellValue::SQUARE_IGNORED),
                    new Solution(3, 3, CellValue::SQUARE_FILLED),
                    new Solution(4, 4, CellValue::SQUARE_IGNORED),
                    new Solution(5, 5, CellValue::SQUARE_FILLED),
                ],
            ],
        ];
    }
}
