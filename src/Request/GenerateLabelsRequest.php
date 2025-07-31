<?php

namespace radz2k\Dpd\Request;

use radz2k\Dpd\Objects\Enum\FileType;
use radz2k\Dpd\Objects\Enum\LabelPrintingPolicy;
use radz2k\Dpd\Objects\Enum\PageSize;
use radz2k\Dpd\Objects\Enum\SessionType;
use radz2k\Dpd\Soap\Types\DpdServicesParamsV1;
use radz2k\Dpd\Soap\Types\GenerateSpedLabelsV1Request;
use radz2k\Dpd\Soap\Types\OutputDocFormatDSPEnumV1;
use radz2k\Dpd\Soap\Types\OutputDocPageFormatDSPEnumV1;
use radz2k\Dpd\Soap\Types\PackageDSPV1;
use radz2k\Dpd\Soap\Types\ParcelDSPV1;
use radz2k\Dpd\Soap\Types\PolicyDSPEnumV1;
use radz2k\Dpd\Soap\Types\SessionDSPV1;
use radz2k\Dpd\Soap\Types\SessionTypeDSPEnumV1;

class GenerateLabelsRequest
{
    private $pageFormat;
    private $pageSize;
    private $parcelIds;
    private $references;
    private $waybills;
    private $printingPolicy;

    /**
     * GenerateLabelsRequest constructor.
     *
     * @param $parcelIds
     * @param $references
     * @param $waybills
     */
    protected function __construct(SessionType $sessionType, array $parcelIds = [], array $references = [], array $waybills = [])
    {
        $this->sessionType = $sessionType;
        $this->parcelIds = $parcelIds;
        $this->references = $references;
        $this->waybills = $waybills;
        $this->pageFormat = FileType::PDF();
        $this->pageSize = PageSize::LBL_PRINTER();
        $this->printingPolicy = LabelPrintingPolicy::STOP_ON_FIRST_ERROR();
    }

    public static function fromParcelIds(SessionType $sessionType, array $parcelIds): GenerateLabelsRequest
    {
        return new static($sessionType, $parcelIds);
    }

    public static function fromReferences(SessionType $sessionType, array $references): GenerateLabelsRequest
    {
        return new static($sessionType, [], $references);
    }

    public static function fromWaybills(SessionType $sessionType, array $waybills): GenerateLabelsRequest
    {
        return new static($sessionType, [], [], $waybills);
    }

    /**
     * @return GenerateSpedLabelsV1Request
     */
    public function toPayload(): GenerateSpedLabelsV1Request
    {
        $request = new GenerateSpedLabelsV1Request();
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
