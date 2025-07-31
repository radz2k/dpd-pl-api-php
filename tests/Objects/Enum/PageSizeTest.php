<?php

namespace radz2k\DpdTests\Objects\Enum;

use PHPUnit\Framework\TestCase;
use radz2k\Dpd\Objects\Enum\PageSize;

class PageSizeTest extends TestCase
{
    /**
     * @dataProvider knownPageSizes
     */
    public function testCreation($pageSize)
    {
        $pageSize = PageSize::$pageSize();
        self::assertEquals($pageSize, (string)$pageSize);
    }

    public function knownPageSizes()
    {
        return [
            ['A4'],
            ['LBL_PRINTER'],
        ];
    }
}
