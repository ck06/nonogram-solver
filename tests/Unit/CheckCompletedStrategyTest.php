<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\Solution;
use App\DTO\Values\CellValue;
use App\Service\NonogramSolverStrategy\CheckCompletedStrategy;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

class CheckCompletedStrategyTest extends RowOrColumnTestCase
{
    #[DataProvider('CheckCompletedStrategyDataProvider')]
    public function testCheckCompletedStrategy($input, $expected): void
    {
        $strategy = new CheckCompletedStrategy();
        $actual = $strategy->tryToSolve($input);
        self::assertEquals($expected, $actual);
    }

    #[DataProvider('CheckCompletedStrategyErrorDataProvider')]
    public function testCheckCompletedStrategyError($input): void
    {
        $this->expectException(RuntimeException::class);
        $strategy = new CheckCompletedStrategy();
        $strategy->tryToSolve($input);
    }

    public static function CheckCompletedStrategyDataProvider(): array
    {
        return [
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐      ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │  ▇  │     │     │  =>  │  ᚷ  │  ▇  │  ▇  │  ᚷ  │  ᚷ  │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘      └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createRowOrColumn([0, 1, 1, 0, 0], '2'),
                'expected' => [
                    new Solution(1, 1, CellValue::SQUARE_IGNORED),
                    new Solution(4, 4, CellValue::SQUARE_IGNORED),
                    new Solution(5, 5, CellValue::SQUARE_IGNORED),
                ],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐      ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │  ▇  │  ᚷ  │  ᚷ  │  ᚷ  │  ᚷ  │  =>  │  ▇  │  ᚷ  │  ᚷ  │  ᚷ  │  ᚷ  │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘      └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createRowOrColumn([1, 2, 2, 2, 2], '1'),
                'expected' => [],
            ],
        ];
    }

    public static function CheckCompletedStrategyErrorDataProvider(): array
    {
        return [
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │  ▇  │  ▇  │     │  =>  ERROR
                // └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createRowOrColumn([0, 1, 1, 1, 0], '2'),
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │     │  ▇  │     │  =>  ERROR
                // └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createRowOrColumn([0, 1, 0, 1, 0], '2'),
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │  ▇  │     │  ▇  │     │  ▇  │  =>  ERROR
                // └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createRowOrColumn([1, 0, 1, 0, 1], '1 1'),
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │  ▇  │  ▇  │  ᚷ  │  ᚷ  │  ᚷ  │  =>  ERROR
                // └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::createRowOrColumn([1, 1, 2, 2, 2], '1'),
            ],
        ];
    }
}
