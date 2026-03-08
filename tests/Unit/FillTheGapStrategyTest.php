<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\Solution;
use App\DTO\Values\CellValue;
use App\Service\NonogramSolverStrategy\FillTheGapStrategy;
use PHPUnit\Framework\Attributes\DataProvider;

class FillTheGapStrategyTest extends RowOrColumnTestCase
{
    #[DataProvider('FillTheGapStrategyDataProvider')]
    public function testFillTheGapStrategy($input, $expected): void
    {
        $strategy = new FillTheGapStrategy();
        $actual = $strategy->tryToSolve($input);

        self::assertEquals($expected, $actual);
    }

    public static function FillTheGapStrategyDataProvider(): array
    {
        return [
            [
                // Too small - should be skipped
                'input' => self::createRowOrColumn([0, 1, 0, 1, 0], '3'),
                'expected' => [],
            ],
            [
                // in:
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │     │     │  ▇  │     │     │     │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                // out:
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │  ▇  │  ▇  │  ▇  │     │     │     │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createRowOrColumn([0, 1, 0, 0, 1, 0, 0, 0, 0, 0], '5'),
                'expected' => [
                    new Solution(2, 5, CellValue::SQUARE_FILLED),
                ],
            ],
            [
                // in:
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │     │  ▇  │     │  ▇  │     │  ▇  │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                // out:
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │     │  ▇  │  ▇  │  ▇  │  ▇  │  ▇  │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createRowOrColumn([0, 1, 0, 1, 0, 1, 0, 1, 0, 0], '1 5'),
                'expected' => [
                    new Solution(4, 8, CellValue::SQUARE_FILLED),
                ],
            ],
        ];
    }
}
