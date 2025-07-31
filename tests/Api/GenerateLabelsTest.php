<?php

namespace radz2k\DpdTests\Api;

use radz2k\Dpd\Objects\Package;
use radz2k\Dpd\Objects\Parcel;
use radz2k\Dpd\Objects\Receiver;
use radz2k\Dpd\Objects\Sender;
use radz2k\Dpd\Objects\Enum\SessionType;
use radz2k\Dpd\Request\GenerateLabelsRequest;
use radz2k\Dpd\Request\GeneratePackageNumbersRequest;

class GenerateLabelsTest extends ApiIntegrationTestCase
{

    public function testA4PDFLabelGenerationForCorrectParcel()
    {

        $sender = new Sender(1495, 501000000, 'XXX',
            'Testowa 21/37', '22555', 'Kraków', 'PL');
        $receiver = new Receiver(605000000, 'YYY',
            'Puławska 2', '02566', 'Warszawa', 'PL');
        $parcel = new Parcel(30, 30, 10, 2);
        $package = new Package($sender, $receiver, [$parcel]);

        $result = self::$api->generatePackageNumbers(GeneratePackageNumbersRequest::fromPackage($package));
        $waybill = $result->getPackages()[0]->getParcels()[0]->getWaybill();

        $result = self::$api->generateLabels(GenerateLabelsRequest::fromWaybills(SessionType::DOMESTIC(), [$waybill]));
        self::assertNotNull($result);
        self::assertNotEmpty($result->getFileContent());
    }

}
