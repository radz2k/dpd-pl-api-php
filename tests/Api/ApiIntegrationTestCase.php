<?php

namespace radz2k\DpdTests\Api;

use PHPUnit\Framework\TestCase;
use radz2k\Dpd\Api;

abstract class ApiIntegrationTestCase extends TestCase
{
    /**
     * @var Api
     */
    protected static $api;

    public static function setUpBeforeClass()
    {
        self::$api = new Api('test', 'thetu4Ee', 1495);
        static::$api->setSandboxMode(true);
    }

}
