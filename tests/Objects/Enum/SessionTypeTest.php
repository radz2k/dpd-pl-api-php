<?php

namespace radz2k\DpdTests\Objects\Enum;

use PHPUnit\Framework\TestCase;
use radz2k\Dpd\Objects\Enum\SessionType;

class SessionTypeTest extends TestCase
{
    /**
     * @dataProvider knownSessionTypes
     */
    public function testCreation($sessionType)
    {
        $sessionType = SessionType::$sessionType();
        self::assertEquals($sessionType, (string)$sessionType);
    }

    public function knownSessionTypes()
    {
        return [
            ['DOMESTIC'],
            ['INTERNATIONAL'],
        ];
    }
}
