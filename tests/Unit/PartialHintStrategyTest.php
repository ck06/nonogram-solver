<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\Solution;
use App\DTO\Values\CellValue;
use App\Service\NonogramSolverStrategy\PartialHintStrategy;
use PHPUnit\Framework\Attributes\DataProvider;

class PartialHintStrategyTest extends RowOrColumnTestCase
{
    #[DataProvider('PartialHintStrategyDataProvider')]
    public function testPartialHintStrategy($input, $expected): void
    {
        $strategy = new PartialHintStrategy();
        $actual = $strategy->tryToSolve($input);

        self::assertEquals($expected, $actual);
    }

    public static function PartialHintStrategyDataProvider(): array
    {
        return [
            [
                // unable to solve
                'input' => self::createBlankRowOrColumn(5, '2'),
                'expected' => [],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │     │  ▇  │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createBlankRowOrColumn(5, '3'),
                'expected' => [
                    new Solution(3, 3, CellValue::SQUARE_FILLED),
                ],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │  ▇  │  ▇  │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createBlankRowOrColumn(5, '4'),
                'expected' => [
                    new Solution(2, 4, CellValue::SQUARE_FILLED),
                ],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │     │  ▇  │  ▇  │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createBlankRowOrColumn(6, '4'),
                'expected' => [
                    new Solution(3, 4, CellValue::SQUARE_FILLED),
                ],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │  ▇  │     │     │  ▇  │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createBlankRowOrColumn(7, '3 2'),
                'expected' => [
                    new Solution(2, 3, CellValue::SQUARE_FILLED),
                    new Solution(6, 6, CellValue::SQUARE_FILLED),
                ],
            ],
            // the below scenarios come with some partially filled input
            [
                // in:
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │     │     │     │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                // out:
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │  ▇  │     │     │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createBlankRowOrColumn(7, '3'),
                'expected' => [
                    new Solution(3, 3, CellValue::SQUARE_FILLED),
                ],
            ],
            [
                // in:
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │     │     │     │     │  ▇  │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                // out:
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │     │     │     │  ▇  │  ▇  │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createRowOrColumn([0, 0, 0, 0, 0, 1, 0], '3'),
                'expected' => [
                    new Solution(5, 5, CellValue::SQUARE_FILLED),
                ],
            ],
            [
                // in:
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │  ▇  │     │     │     │     │     │  ▇  │     │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                // out:
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │  ▇  │  ▇  │  ▇  │  ▇  │  ᚷ  │     │  ▇  │  ▇  │     │  ᚷ  │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createRowOrColumn([1, 0, 0, 0, 0, 0, 1, 0, 0, 0], '4 3'),
                'expected' => [
                    new Solution(2, 4, CellValue::SQUARE_FILLED),
                    new Solution(5, 5, CellValue::SQUARE_IGNORED),
                    new Solution(8, 8, CellValue::SQUARE_FILLED),
                ],
            ],
        ];
    }
}
