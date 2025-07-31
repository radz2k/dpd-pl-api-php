<?php

namespace radz2k\DpdTests\Request;

use PHPUnit\Framework\TestCase;
use radz2k\Dpd\Objects\Enum\FileType;
use radz2k\Dpd\Objects\Enum\LabelPrintingPolicy;
use radz2k\Dpd\Objects\Enum\PageSize;
use radz2k\Dpd\Objects\Enum\SessionType;
use radz2k\Dpd\Request\GenerateLabelsRequest;

class GenerateLabelsRequestTest extends TestCase
{
    public function testCreationFromParcelIds()
    {
        $request = GenerateLabelsRequest::fromParcelIds(SessionType::DOMESTIC(),[42, 43, 44]);
        $payload = $request->toPayload();

        list($package) = $payload->getDpdServicesParams()->getSession()->getPackages();
        self::assertCount(3, $package->getParcels());
        list($parcel1, $parcel2, $parcel3) = $package->getParcels();

        $parcelIds = [$parcel1->getParcelId(), $parcel2->getParcelId(), $parcel3->getParcelId()];
        sort($parcelIds);
        self::assertSame([42, 43, 44], $parcelIds);

        $parcelReferences = [$parcel1->getReference(), $parcel2->getReference(), $parcel3->getReference()];
        self::assertSame([null, null, null], $parcelReferences);

        $waybills = [$parcel1->getWaybill(), $parcel2->getWaybill(), $parcel3->getWaybill()];
        self::assertSame([null, null, null], $waybills);

        self::assertEquals(PageSize::A4(), (string)$payload->getOutputDocPageFormat());
        self::assertEquals(FileType::PDF(), (string)$payload->getOutputDocFormat());
        self::assertEquals(LabelPrintingPolicy::IGNORE_ERRORS(), (string)$payload->getDpdServicesParams()->getPolicy());
        self::assertEquals(SessionType::DOMESTIC(), (string)$payload->getDpdServicesParams()->getSession()->getSessionType());
        self::assertNull($payload->getDpdServicesParams()->getDocumentId());
        self::assertNull($payload->getDpdServicesParams()->getPickupAddress());
        self::assertNull($payload->getDpdServicesParams()->getSession()->getSessionId());
        self::assertNull($payload->getAuthData());
    }

    public function testCreationFromReferences()
    {
        $request = GenerateLabelsRequest::fromReferences(SessionType::DOMESTIC(),['Reference1', 'Reference2', 'Reference3']);
        $payload = $request->toPayload();

        list($package) = $payload->getDpdServicesParams()->getSession()->getPackages();
        self::assertCount(3, $package->getParcels());
        list($parcel1, $parcel2, $parcel3) = $package->getParcels();

        $parcelIds = [$parcel1->getParcelId(), $parcel2->getParcelId(), $parcel3->getParcelId()];
        self::assertSame([null, null, null], $parcelIds);

        $parcelReferences = [$parcel1->getReference(), $parcel2->getReference(), $parcel3->getReference()];
        sort($parcelReferences);
        self::assertSame(['Reference1', 'Reference2', 'Reference3'], $parcelReferences);

        $waybills = [$parcel1->getWaybill(), $parcel2->getWaybill(), $parcel3->getWaybill()];
        self::assertSame([null, null, null], $waybills);

        self::assertEquals(PageSize::A4(), (string)$payload->getOutputDocPageFormat());
        self::assertEquals(FileType::PDF(), (string)$payload->getOutputDocFormat());
        self::assertEquals(LabelPrintingPolicy::IGNORE_ERRORS(), (string)$payload->getDpdServicesParams()->getPolicy());
        self::assertEquals(SessionType::DOMESTIC(), (string)$payload->getDpdServicesParams()->getSession()->getSessionType());
        self::assertNull($payload->getDpdServicesParams()->getDocumentId());
        self::assertNull($payload->getDpdServicesParams()->getPickupAddress());
        self::assertNull($payload->getDpdServicesParams()->getSession()->getSessionId());
        self::assertNull($payload->getAuthData());
    }

    public function testCreationFromWaybills()
    {
        $request = GenerateLabelsRequest::fromWaybills(SessionType::DOMESTIC(),['Waybill1', 'Waybill2', 'Waybill3']);
        $payload = $request->toPayload();

        list($package) = $payload->getDpdServicesParams()->getSession()->getPackages();
        self::assertCount(3, $package->getParcels());
        list($parcel1, $parcel2, $parcel3) = $package->getParcels();

        $parcelIds = [$parcel1->getParcelId(), $parcel2->getParcelId(), $parcel3->getParcelId()];
        self::assertSame([null, null, null], $parcelIds);

        $parcelReferences = [$parcel1->getReference(), $parcel2->getReference(), $parcel3->getReference()];
        self::assertSame([null, null, null], $parcelReferences);

        $waybills = [$parcel1->getWaybill(), $parcel2->getWaybill(), $parcel3->getWaybill()];
        sort($waybills);
        self::assertSame(['Waybill1', 'Waybill2', 'Waybill3'], $waybills);

        self::assertEquals(PageSize::A4(), (string)$payload->getOutputDocPageFormat());
        self::assertEquals(FileType::PDF(), (string)$payload->getOutputDocFormat());
        self::assertEquals(LabelPrintingPolicy::IGNORE_ERRORS(), (string)$payload->getDpdServicesParams()->getPolicy());
        self::assertEquals(SessionType::DOMESTIC(), (string)$payload->getDpdServicesParams()->getSession()->getSessionType());
        self::assertNull($payload->getDpdServicesParams()->getDocumentId());
        self::assertNull($payload->getDpdServicesParams()->getPickupAddress());
        self::assertNull($payload->getDpdServicesParams()->getSession()->getSessionId());
        self::assertNull($payload->getAuthData());
    }
}
