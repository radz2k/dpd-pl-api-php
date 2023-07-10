<?php

namespace radz2k\Dpd\Request;

use radz2k\Dpd\Objects\Enum\FileType;
use radz2k\Dpd\Objects\Enum\LabelPrintingPolicy;
use radz2k\Dpd\Objects\Enum\PageSize;
use radz2k\Dpd\Objects\Enum\SessionType;
use radz2k\Dpd\Soap\Types\DpdServicesParamsV1;
use radz2k\Dpd\Soap\Types\GenerateProtocolV2Request;
use radz2k\Dpd\Soap\Types\OutputDocFormatDSPEnumV1;
use radz2k\Dpd\Soap\Types\OutputDocPageFormatDSPEnumV1;
use radz2k\Dpd\Soap\Types\PackageDSPV1;
use radz2k\Dpd\Soap\Types\ParcelDSPV1;
use radz2k\Dpd\Soap\Types\PolicyDSPEnumV1;
use radz2k\Dpd\Soap\Types\SessionDSPV1;
use radz2k\Dpd\Soap\Types\SessionTypeDSPEnumV1;

class GenerateProtocolRequest
{
    private $pageFormat;
    private $pageSize;
    private $parcelIds;
    private $references;
    private $waybills;
    private $printingPolicy;

    /**
     * GenerateProtocolRequest constructor.
     *
     * @param $parcelIds
     * @param $references
     * @param $waybills
     */
    protected function __construct(SessionType $sessionType, array $parcelIds, array $references, array $waybills)
    {
        $this->sessionType = $sessionType;
        $this->parcelIds = $parcelIds;
        $this->references = $references;
        $this->waybills = $waybills;
        $this->pageFormat = FileType::PDF();
        $this->pageSize = PageSize::A4();
        $this->printingPolicy = LabelPrintingPolicy::IGNORE_ERRORS();
    }

    public static function fromParcelIds(SessionType $sessionType, array $parcelIds): GenerateProtocolRequest
    {
        return new static($sessionType, $parcelIds, [], []);
    }

    public static function fromReferences(SessionType $sessionType, array $references): GenerateProtocolRequest
    {
        return new static($sessionType, [], $references, []);
    }

    public static function fromWaybills(SessionType $sessionType, array $waybills): GenerateProtocolRequest
    {
        return new static($sessionType, [], [], $waybills);
    }

    /**
     * @return GenerateProtocolV2Request
     */
    public function toPayload(): GenerateProtocolV2Request
    {
        $request = new GenerateProtocolV2Request();
        $request->setOutputDocFormat(new OutputDocFormatDSPEnumV1((string) $this->pageFormat));
        $request->setOutputDocPageFormat(new OutputDocPageFormatDSPEnumV1((string) $this->pageSize));

        $serviceParams = new DpdServicesParamsV1();
        $serviceParams->setPolicy(new PolicyDSPEnumV1((string) $this->printingPolicy));

        $session = new SessionDSPV1();
        $session->setSessionType(new SessionTypeDSPEnumV1((string) $this->sessionType));
        
        $parcels = [];
        if (!empty($this->parcelIds)) {
            foreach ($this->parcelIds as $parcelId) {
                $parcel = new ParcelDSPV1();
                $parcel->setParcelId($parcelId);
                $parcels[] = $parcel;
            }
        }

        if (!empty($this->references)) {
            foreach ($this->references as $reference) {
                //$package = new PackageDSPV1();
                $parcel = new ParcelDSPV1();
                $parcel->setReference($reference);
                $parcels[] = $parcel;
            }
        }

        if (!empty($this->waybills)) {
            foreach ($this->waybills as $waybill) {
                //$package = new PackageDSPV1();
                $parcel = new ParcelDSPV1();
                $parcel->setWaybill($waybill);
                $parcels[] = $parcel;
            }
        }
        $package = new PackageDSPV1();
        $package->setParcels($parcels);
        $session->setPackages([$package]);
        $serviceParams->setSession($session);
        $request->setDpdServicesParams($serviceParams);

        return $request;
    }
}
