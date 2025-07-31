<?php
namespace radz2k\DpdTests\Objects;

use PHPUnit\Framework\TestCase;
use radz2k\Dpd\Objects\CourierAvailability;

class CourierAvailabilityTest extends TestCase
{

    public function testCreation()
    {
        $courierAvailability = new CourierAvailability('offset', 'range');
        self::assertEquals('offset', $courierAvailability->getOffset());
        self::assertEquals('range', $courierAvailability->getRange());
    }
}
