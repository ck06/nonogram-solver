<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\RowOrColumn;
use App\DTO\Solution;
use App\DTO\Values\CellValue;
use App\Service\NonogramSolverStrategy\FillTheGapStrategy;
use App\Service\NonogramSolverStrategy\StandaloneHintStrategy;
use PHPUnit\Framework\TestCase;

class FillTheGapStrategyTest extends TestCase
{
    /** @dataProvider FillTheGapStrategyDataProvider */
    public function testFillTheGapStrategy($input, $expected): void
    {
        $strategy = new FillTheGapStrategy();
        $actual = $strategy->tryToSolve($input);

        $this->assertEquals($expected, $actual);
    }

    public function FillTheGapStrategyDataProvider(): array
    {
        return [
            [
                // Too small - should be skipped
                'input' => [new RowOrColumn([0, 1, 0, 1, 0], '3')],
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
                'input' => [new RowOrColumn([0, 1, 0, 0, 1, 0, 0, 0, 0, 0], '5')],
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
                'input' => [new RowOrColumn([0, 1, 0, 1, 0, 1, 0, 1, 0, 0], '1 5')],
                'expected' => [
                    new Solution(4, 8, CellValue::SQUARE_FILLED),
                ],
            ],
        ];
    }
}
