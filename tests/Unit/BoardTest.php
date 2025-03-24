<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\Board;
use App\DTO\Values\GridBoardOutput;
use App\DTO\Values\GridSquareValue;
use App\Service\NonogramSolver;
use PHPUnit\Framework\TestCase;

/**
 * Example of expected JSON
 * {
 *     'height': 10,
 *     'width': 10,
 *     'hints': {
 *         'column': ['6', '3 2', '1 3', '4', '2', '1 4', '2 2', '4', '2 2', '5'],
 *         'row': ['4 1 3', '2 2 4', '5 2 1', '1 2 3', '3 2', '2', '2', '2', '1', '1']
 *     },
 *     'data': [                // 0 = open, 1 = filled in, 2 = crossed out
 *         '1111200000',
 *         '1121100000',
 *         '1111100000',
 *         '1211200000',
 *         '1112200000',
 *         '1122222222',
 *         '2222211222',
 *         '2222211222',
 *         '2222212222',
 *         '2222212222'
 *     ]
 * }
 */
class BoardTest extends TestCase
{
    public function testBoardDrawing(): void
    {
        $board = new Board($this->get3x3TestData());
        $board->updateMultipleInRow(1, 1, 3, GridSquareValue::SQUARE_FILLED);
        $board->updateSquare(2, 1, GridSquareValue::SQUARE_IGNORED);
        $board->updateSquare(2, 2, GridSquareValue::SQUARE_FILLED);
        $board->updateSquare(2, 3, GridSquareValue::SQUARE_IGNORED);

        $expected = '
  ┌―――――┬―――――┬―――――┐  
  │  ▇  │  ▇  │  ▇  │  
  ├―――――┼―――――┼―――――┤  
  │  ᚷ  │  ▇  │  ᚷ  │  
  ├―――――┼―――――┼―――――┤  
  │     │     │     │  
  └―――――┴―――――┴―――――┘  
';

        $actual = $board->draw();
        $this->assertSame(trim($expected), trim($actual));
        dump($actual);
    }

    public function testRowAndColumnUpdateSimultaneously(): void
    {
        $board = new Board($this->get3x3TestData());

        // solving row 1 should automatically update all 3 columns
        $board->updateMultipleInRow(1, 1, 3, GridSquareValue::SQUARE_FILLED);
        $this->assertSame(['1' => 1, '2' => 1, '3' => 1], $board->getRows()[1]);
        $this->assertSame(['1' => 1, '2' => 0, '3' => 0], $board->getColumns()[1]);

        $board->updateMultipleInColumn(2, 1, 3, GridSquareValue::SQUARE_FILLED);
        $this->assertSame(['1' => 0, '2' => 1, '3' => 0], $board->getRows()[2]);
        $this->assertSame(['1' => 1, '2' => 1, '3' => 1], $board->getColumns()[2]);

        // check the whole grid as well to be sure
        $board->updateMultipleInRow(3, 1, 3, GridSquareValue::SQUARE_FILLED);
        $this->assertSame([
            '1' => ['1' => 1, '2' => 1, '3' => 1],
            '2' => ['1' => 0, '2' => 1, '3' => 0],
            '3' => ['1' => 1, '2' => 1, '3' => 1],
        ], $board->getGrid());
    }

    public function testSolver3x3(): void
    {
        $solver = new NonogramSolver();
        $board = new Board($this->get3x3TestData());
        $expected = [
            '1' => ['1' => 1, '2' => 1, '3' => 1],
            '2' => ['1' => 0, '2' => 1, '3' => 0],
            '3' => ['1' => 1, '2' => 1, '3' => 1],
        ];

        $actual = $solver->solve($board);

        $this->assertSame($expected, $actual);
    }

    private function get3x3TestData(): string
    {
        // solution:
        // ■ ■ ■
        // x ■ x
        // ■ ■ ■
        $testData = [
            'height' => 3,
            'width' => 3,
            'hints' => [
                'row' => [
                    '3',
                    '1',
                    '3',
                ],
                'column' => [
                    '1 1',
                    '3',
                    '1 1',
                ],
            ],
            'data' => [],
        ];

        return json_encode($testData, JSON_THROW_ON_ERROR);
    }

    private function get5x5TestData(): string
    {
        // solution:
        // ■ ■ ■
        // 🅇 ■ 🅇
        // ■ ■ ■
        $testData = [
            'height' => 3,
            'width' => 3,
            'hints' => [
                'row' => [
                    '3',
                    '1',
                    '3',
                ],
                'column' => [
                    '1 1',
                    '3',
                    '1 1',
                ],
            ],
            'data' => [],
        ];

        return json_encode($testData, JSON_THROW_ON_ERROR);
    }
}
