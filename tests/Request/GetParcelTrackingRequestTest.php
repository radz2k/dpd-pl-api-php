<?php

namespace radz2k\DpdTests\Request;

use PHPUnit\Framework\TestCase;
use radz2k\Dpd\Objects\Enum\TrackingEventsCount;
use radz2k\Dpd\Request\GetParcelTrackingRequest;

class GetParcelTrackingRequestTest extends TestCase
{
    public function testCreation()
    {
        $request = GetParcelTrackingRequest::fromWaybill('waybill', TrackingEventsCount::ALL());
        $payload = $request->toPayload();
        self::assertEquals('waybill', $payload->getWaybill());
        self::assertEquals('PL', $payload->getLanguage());
        self::assertEquals('ALL', (string)$payload->getEventsSelectType());

        $request = GetParcelTrackingRequest::fromWaybill('waybill', TrackingEventsCount::ALL(), 'EN');
        $payload = $request->toPayload();
        self::assertEquals('EN', $payload->getLanguage());

    }
}
