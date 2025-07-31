<?php

namespace radz2k\DpdTests\Request;

use PHPUnit\Framework\TestCase;
use radz2k\Dpd\Request\FindPostalCodeRequest;

class FindPostalCodeRequestTest extends TestCase
{
    public function testCreationWithoutCountryCode()
    {
        $request = FindPostalCodeRequest::from('Test postal code');
        $payload = $request->toPayload();
        self::assertEquals('Test postal code', $payload->getPostalCode()->getZipCode());
        self::assertEquals('PL', $payload->getPostalCode()->getCountryCode());
        self::assertNull($payload->getAuthData());
    }

    public function testCreationWithCountryCode()
    {
        $request = FindPostalCodeRequest::from('Test postal code', 'DE');
        $payload = $request->toPayload();
        self::assertEquals('Test postal code', $payload->getPostalCode()->getZipCode());
        self::assertEquals('DE', $payload->getPostalCode()->getCountryCode());
        self::assertNull($payload->getAuthData());
    }
}
