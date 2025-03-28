<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\Board;
use App\DTO\Values\CellValue;
use App\Service\NonogramSolver;
use App\Service\NonogramSolverStrategy\StandaloneHintStrategy;
use App\Service\NonogramSolverStrategy\StrategyCollection;
use PHPUnit\Framework\TestCase;

/**
 * Example of expected JSON
 * {
 *     'height': 12,
 *     'width': 12,
 *     'hints': {
 *         'column': ['6', '3 2', '1 3', '4', '2', '1 4', '2 2', '4', '2 2', '5'],
 *         'row': ['4 1 3', '2 2 4', '5 2 1', '1 2 3', '3 2', '2', '2', '2', '1', '1']
 *     },
 *     'data': [                // 2 = open, 1 = filled in, 2 = crossed out
 *         '1111222222',
 *         '1121122222',
 *         '1111122222',
 *         '1211222222',
 *         '1112222222',
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
    public function setUp(): void
    {
        $this->solver = new NonogramSolver(
            new StrategyCollection(
                [
                    new StandaloneHintStrategy(),
                ],
            )
        );
    }

    public function testBoardDrawing(): void
    {
        $board = new Board($this->dataProvider3x3()[0][0]);
        $board->updateMultipleInRow(1, 1, 3, CellValue::SQUARE_FILLED);
        $board->updateCell(2, 1, CellValue::SQUARE_IGNORED);
        $board->updateCell(2, 2, CellValue::SQUARE_FILLED);
        $board->updateCell(2, 3, CellValue::SQUARE_IGNORED);

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
        $board = new Board($this->dataProvider3x3()[0][0]);

        // solving row 1 should automatically update all 3 columns
        $board->updateMultipleInRow(1, 1, 3, CellValue::SQUARE_FILLED);
        $this->assertSame(['1' => 1, '2' => 1, '3' => 1], $board->getRow(1)->getData());
        $this->assertSame(['1' => 1, '2' => 0, '3' => 0], $board->getColumn(1)->getData());

        $board->updateMultipleInColumn(2, 1, 3, CellValue::SQUARE_FILLED);
        $this->assertSame(['1' => 0, '2' => 1, '3' => 0], $board->getRow(2)->getData());
        $this->assertSame(['1' => 1, '2' => 1, '3' => 1], $board->getColumn(2)->getData());

        // check the whole grid as well to be sure
        $board->updateMultipleInRow(3, 1, 3, CellValue::SQUARE_FILLED);
        $this->assertSame([
            '1' => ['1' => 1, '2' => 1, '3' => 1],
            '2' => ['1' => 0, '2' => 1, '3' => 0],
            '3' => ['1' => 1, '2' => 1, '3' => 1],
        ], $board->getGrid());
    }

    /**
     * @dataProvider dataProvider3x3
     */
    public function testSolver3x3(string $input, array $expected): void
    {
        $solvedBoard = $this->solver->solve(new Board($input));
        $actual = $solvedBoard->getGrid();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider dataProvider5x5
     */
    public function testSolver5x5(string $input, array $expected): void
    {
        $solvedBoard = $this->solver->solve(new Board($input));
        $actual = $solvedBoard->getGrid();

        $this->assertSame($expected, $actual);
    }

    /**
     *  ┌―――――┬―――――┬―――――┐
     *  │  ▇  │  ▇  │  ▇  │
     *  ├―――――┼―――――┼―――――┤
     *  │  ᚷ  │  ▇  │  ᚷ  │
     *  ├―――――┼―――――┼―――――┤
     *  │  ▇  │  ▇  │  ▇  │
     *  └―――――┴―――――┴―――――┘
     */
    private function dataProvider3x3(): array
    {
        $testData = [
            'height' => 3,
            'width' => 3,
            'hints' => [
                'row' => ['1' => '3', '2' => '1', '3' => '3'],
                'column' => ['1' => '1 1', '2' => '3', '3' => '1 1'],
            ],
            'data' => [],
        ];

        $solution = [
            '1' => ['1' => 1, '2' => 1, '3' => 1],
            '2' => ['1' => 2, '2' => 1, '3' => 2],
            '3' => ['1' => 1, '2' => 1, '3' => 1],
        ];

        return [
            [
                json_encode($testData, JSON_THROW_ON_ERROR),
                $solution,
            ]
        ];
    }

    /**
     *  ┌―――――┬―――――┬―――――┬―――――┬―――――┐
     *  │  ▇  │  ᚷ  │  ▇  │  ᚷ  │  ▇  │
     *  ├―――――┼―――――┼―――――┼―――――┼―――――┤
     *  │  ᚷ  │  ▇  │  ▇  │  ▇  │  ᚷ  │
     *  ├―――――┼―――――┼―――――┼―――――┼―――――┤
     *  │  ▇  │  ▇  │  ▇  │  ▇  │  ▇  │
     *  ├―――――┼―――――┼―――――┼―――――┼―――――┤
     *  │  ᚷ  │  ▇  │  ▇  │  ▇  │  ᚷ  │
     *  ├―――――┼―――――┼―――――┼―――――┼―――――┤
     *  │  ▇  │  ᚷ  │  ▇  │  ᚷ  │  ▇  │
     *  └―――――┴―――――┴―――――┴―――――┴―――――┘
     */
    private function dataProvider5x5(): array
    {
        $testData = [
            'height' => 5,
            'width' => 5,
            'hints' => [
                'row' => ['1' => '1 1 1', '2' => '3', '3' => '5', '4' => '3', '5' => '1 1 1'],
                'column' => ['1' => '1 1 1', '2' => '3', '3' => '5', '4' => '3', '5' => '1 1 1'],
            ],
            'data' => [],
        ];

        $solution = [
            '1' => ['1' => 1, '2' => 2, '3' => 1, '4' => 2, '5' => 1],
            '2' => ['1' => 2, '2' => 1, '3' => 1, '4' => 1, '5' => 2],
            '3' => ['1' => 1, '2' => 1, '3' => 1, '4' => 1, '5' => 1],
            '4' => ['1' => 2, '2' => 1, '3' => 1, '4' => 1, '5' => 2],
            '5' => ['1' => 1, '2' => 2, '3' => 1, '4' => 2, '5' => 1],
        ];

        return [
            [
                json_encode($testData, JSON_THROW_ON_ERROR),
                $solution,
            ]
        ];
    }
}
