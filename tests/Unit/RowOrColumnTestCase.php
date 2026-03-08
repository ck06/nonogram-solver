<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\Board;
use App\DTO\RowOrColumn;
use PHPUnit\Framework\TestCase;

abstract class RowOrColumnTestCase extends TestCase
{
    public static function createRowOrColumn(array $cells, string $hints): RowOrColumn
    {
        $board = new Board(json_encode([
            'height' => 1,
            'width' => count($cells),
            'hints' => [
                'column' => [],
                'row' => [
                    '1' => $hints
                ],
            ],
            'data' => [
                '1' => implode('', $cells),
            ],
        ], JSON_THROW_ON_ERROR));

        return $board->getRow(1);
    }

    public static function createBlankRowOrColumn(int $size, string $hints): RowOrColumn
    {
        $data = [];
        for ($i = 1; $i <= $size; $i++) {
            $data[(string)($i + 1)] = 0;
        }

        return self::createRowOrColumn($data, $hints);
    }
}
