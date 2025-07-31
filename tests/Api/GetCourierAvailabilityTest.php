<?php

namespace radz2k\DpdTests\Api;

use radz2k\Dpd\Request\GetCourierAvailabilityRequest;

class GetCourierAvailabilityTest extends ApiIntegrationTestCase
{

    public function testGetCourierAvailability()
    {

        $result = self::$api->getCourierAvailability(GetCourierAvailabilityRequest::from('05807', 'PL'));
        self::assertNotNull($result);
        self::assertNotEmpty($result->getRanges());
    }

}
