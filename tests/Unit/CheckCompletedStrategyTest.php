<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\RowOrColumn;
use App\DTO\Solution;
use App\DTO\Values\CellValue;
use App\Service\NonogramSolverStrategy\CheckCompletedStrategy;
use App\Service\NonogramSolverStrategy\StandaloneHintStrategy;
use PHPUnit\Framework\TestCase;

class CheckCompletedStrategyTest extends TestCase
{
    /** @dataProvider CheckCompletedStrategyDataProvider */
    public function testCheckCompletedStrategy($input, $expected): void
    {
        $strategy = new CheckCompletedStrategy();
        $actual = $strategy->tryToSolve($input);

        $this->assertEquals($expected, $actual);
    }

    public function CheckCompletedStrategyDataProvider(): array
    {
        return [
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐      ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │  ▇  │     │     │  =>  │  ᚷ  │  ▇  │  ▇  │  ᚷ  │  ᚷ  │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘      └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => [new RowOrColumn([0, 1, 1, 0, 0], '2')],
                'expected' => [
                    new Solution(1,1,CellValue::SQUARE_IGNORED),
                    new Solution(4,5,CellValue::SQUARE_IGNORED),
                ],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐      ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │  ▇  │  ▇  │     │  =>  │     │     │     │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘      └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => [new RowOrColumn([0, 1, 1, 1, 0], '2')],
                'expected' => [
                    new Solution(1,5,CellValue::SQUARE_IGNORED),
                ],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐      ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │     │  ▇  │     │  =>  │     │     │     │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘      └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => [new RowOrColumn([0, 1, 0, 1, 0], '2')],
                'expected' => [
                    new Solution(1,5,CellValue::SQUARE_IGNORED),
                ],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐      ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │  ▇  │     │  ▇  │     │  ▇  │  =>  │     │     │     │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘      └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => [new RowOrColumn([1, 0, 1, 0, 1], '1 1')],
                'expected' => [
                    new Solution(1,5,CellValue::SQUARE_IGNORED),
                ],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐      ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │  ▇  │  ᚷ  │  ᚷ  │  ᚷ  │  ᚷ  │  =>  │  ▇  │  ᚷ  │  ᚷ  │  ᚷ  │  ᚷ  │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘      └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => [new RowOrColumn([1, 2, 2, 2, 2], '1')],
                'expected' => [],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐      ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │  ▇  │  ▇  │  ᚷ  │  ᚷ  │  ᚷ  │  =>  │     │     │     │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘      └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => [new RowOrColumn([1, 1, 2, 2, 2], '1')],
                'expected' => [
                    new Solution(1,5,CellValue::SQUARE_IGNORED),
                ],
            ],
        ];
    }
}
