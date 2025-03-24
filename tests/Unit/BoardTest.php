<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\Board;
use App\DTO\Values\GridSquareValue;
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
    public function testRowAndColumnUpdateSimultaneously(): void
    {
        $board = new Board($this->getVeryEasyTestData());

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

    private function getVeryEasyTestData(): string
    {
        // test data is the letter I;
        // ■ ■ ■
        // x ■ x
        // ■ ■ ■
        $testData = [
            'height' => 3,
            'width' => 3,
            'hints' => [
                'column' => [
                    '1 1',
                    '3',
                    '1 1',
                ],
                'row' => [
                    '3',
                    '1',
                    '3',
                ]
            ],
            'data' => [],
        ];

        return json_encode($testData, JSON_THROW_ON_ERROR);
    }
}
