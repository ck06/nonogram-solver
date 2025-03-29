<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\RowOrColumn;
use App\DTO\Solution;
use App\DTO\Values\CellValue;
use App\Service\NonogramSolverStrategy\PartialHintStrategy;
use App\Service\NonogramSolverStrategy\StandaloneHintStrategy;
use PHPUnit\Framework\TestCase;

class PartialHintStrategyTest extends TestCase
{
    /** @dataProvider PartialHintStrategyDataProvider */
    public function testPartialHintStrategy($input, $expected): void
    {
        $strategy = new PartialHintStrategy();
        $actual = $strategy->tryToSolve($input);

        $this->assertEquals($expected, $actual);
    }

    public function PartialHintStrategyDataProvider(): array
    {
        return [
            [
                // unable to solve
                'input' => self::GetBlankRowOrColumn(5, '2'),
                'expected' => [],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │     │  ▇  │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::GetBlankRowOrColumn(5, '3'),
                'expected' => [
                    new Solution(3, 3, CellValue::SQUARE_FILLED),
                ],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │  ▇  │  ▇  │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::GetBlankRowOrColumn(5, '4'),
                'expected' => [
                    new Solution(2, 4, CellValue::SQUARE_FILLED),
                ],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │     │  ▇  │  ▇  │     │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::GetBlankRowOrColumn(6, '4'),
                'expected' => [
                    new Solution(3, 4, CellValue::SQUARE_FILLED),
                ],
            ],
            [
                // ┌―――――┬―――――┬―――――┬―――――┬―――――┬―――――┬―――――┐
                // │     │  ▇  │  ▇  │     │     │  ▇  │     │
                // └―――――┴―――――┴―――――┴―――――┴―――――┴―――――┴―――――┘
                'input' => self::GetBlankRowOrColumn(7, '3 2'),
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
                'input' => self::GetBlankRowOrColumn(7, '3'),
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
                'input' => new RowOrColumn([0, 0, 0, 0, 0, 1, 0], '3'),
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
                'input' => new RowOrColumn([1, 0, 0, 0, 0, 0, 1, 0, 0, 0], '4 3'),
                'expected' => [
                    new Solution(2, 4, CellValue::SQUARE_FILLED),
                    new Solution(5, 5, CellValue::SQUARE_IGNORED),
                    new Solution(8, 8, CellValue::SQUARE_FILLED),
                ],
            ],
        ];
    }

    private static function GetBlankRowOrColumn(int $size, string $hints): RowOrColumn
    {
        $data = [];
        for ($i = 1; $i <= $size; $i++) {
            $data[] = 0;
        }

        return new RowOrColumn($data, $hints);
    }
}
