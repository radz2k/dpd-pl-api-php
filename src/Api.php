<?php

namespace radz2k\Dpd;

use Phpro\SoapClient\Soap\DefaultEngineFactory;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMap;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMapCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use radz2k\Dpd\Request\CollectionOrderRequest;
use radz2k\Dpd\Request\FindPostalCodeRequest;
use radz2k\Dpd\Request\GenerateLabelsRequest;
use radz2k\Dpd\Request\GeneratePackageNumbersRequest;
use radz2k\Dpd\Request\GenerateInternationalPackageNumbersRequest;
use radz2k\Dpd\Request\GenerateProtocolRequest;
use radz2k\Dpd\Request\GetCourierAvailabilityRequest;
use radz2k\Dpd\Request\GetParcelTrackingRequest;
use radz2k\Dpd\Response\CollectionOrderResponse;
use radz2k\Dpd\Response\FindPostalCodeResponse;
use radz2k\Dpd\Response\GenerateLabelsResponse;
use radz2k\Dpd\Response\GeneratePackageNumbersResponse;
use radz2k\Dpd\Response\GenerateInternationalPackageNumbersResponse;
use radz2k\Dpd\Response\GenerateProtocolResponse;
use radz2k\Dpd\Response\GetCourierAvailabilityResponse;
use radz2k\Dpd\Response\GetParcelTrackingResponse;
use radz2k\Dpd\Soap\Client\AppServicesClient;
use radz2k\Dpd\Soap\Client\InfoServicesClient;
use radz2k\Dpd\Soap\Client\PackageServicesClient;
use radz2k\Dpd\Soap\Types\AuthDataV1;
use Debugbar;

class Api {

    const PACKAGESERVICE_SANDBOX_WSDL_URL = 'http://dpdservicesdemo.dpd.com.pl/DPDPackageObjServicesService/DPDPackageObjServices?wsdl';
    const PACKAGESERVICE_PRODUCTION_WSDL_URL = 'http://dpdservices.dpd.com.pl/DPDPackageObjServicesService/DPDPackageObjServices?wsdl';
    const APPSERVICE_SANDBOX_WSDL_URL = 'http://dpdappservicesdemo.dpd.com.pl/DPDCRXmlServicesService/DPDCRXmlServices?wsdl';
    const APPSERVICE_PRODUCTION_WSDL_URL = 'http://dpdappservices.dpd.com.pl/DPDCRXmlServicesService/DPDCRXmlServices?wsdl';
    const INFOSERVICE_SANDBOX_WSDL_URL = null;
    const INFOSERVICE_PRODUCTION_WSDL_URL = 'https://dpdinfoservices.dpd.com.pl/DPDInfoServicesObjEventsService/DPDInfoServicesObjEvents?wsdl';

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $masterFid;

    /**
     * @var bool
     */
    private $sandboxMode = false;

    /**
     * @var PackageServicesClient
     */
    private $packageServicesClient;

    /**
     * @var AppServicesClient
     */
    private $appServicesClient;

    /**
     * @var InfoServicesClient
     */
    private $infoServicesClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Api constructor.
     *
     * @param string $login
     * @param string $password
     * @param int    $masterFid
     */
    public function __construct(string $login, string $password, int $masterFid) {
        $this->login = $login;
        $this->password = $password;
        $this->masterFid = $masterFid;
    }

    /**
     * @return string
     */
    public function getLogin(): string {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login) {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password) {
        $this->password = $password;
    }

    /**
     * @return int
     */
    public function getMasterFid(): int {
        return $this->masterFid;
    }

    /**
     * @param int $masterFid
     */
    public function setMasterFid(int $masterFid) {
        $this->masterFid = $masterFid;
    }

    /**
     * @return bool
     */
    public function isSandboxMode(): bool {
        return $this->sandboxMode;
    }

    /**
     * @param bool $sandboxMode
     */
    public function setSandboxMode(bool $sandboxMode) {
        $this->sandboxMode = $sandboxMode;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * @param string $clientClass
     *
     * @return string
     */
    private function getWsdl($clientClass) {
        if ($this->sandboxMode) {
            switch ($clientClass) {
                case PackageServicesClient::class:
                    return self::PACKAGESERVICE_SANDBOX_WSDL_URL;
                case AppServicesClient::class:
                    return self::APPSERVICE_SANDBOX_WSDL_URL;
                case InfoServicesClient::class:
                    //InfoServices endpoint has no sandbox mode - using production instead
                    return self::INFOSERVICE_PRODUCTION_WSDL_URL;
            }
        }

        switch ($clientClass) {
            case PackageServicesClient::class:
                return self::PACKAGESERVICE_PRODUCTION_WSDL_URL;
            case AppServicesClient::class:
                return self::APPSERVICE_PRODUCTION_WSDL_URL;
            case InfoServicesClient::class:
                return self::INFOSERVICE_PRODUCTION_WSDL_URL;
        }
    }

    /**
     * @return PackageServicesClient
     */
    private function obtainPackageServiceClient() {
        if ($this->packageServicesClient === null) {
            $this->packageServicesClient = $this->obtainClient(PackageServicesClient::class);
        }

        return $this->packageServicesClient;
    }

    /**
     * @return AppServicesClient
     */
    private function obtainAppServiceClient() {
        if ($this->appServicesClient === null) {
            $this->appServicesClient = $this->obtainClient(AppServicesClient::class);
        }

        return $this->appServicesClient;
    }

    /**
     * @return InfoServicesClient
     */
    private function obtainInfoServiceClient() {
        if ($this->infoServicesClient === null) {
            $this->infoServicesClient = $this->obtainClient(InfoServicesClient::class);
        }

        return $this->infoServicesClient;
    }

    /**
     * @param $clientClass
     *
     */
    private function obtainClient($clientClass) {
        $wsdl = $this->getWsdl($clientClass,
                [
                    'cache_wsdl' => WSDL_CACHE_NONE,
                ]
        );

        $engine = DefaultEngineFactory::create(
                        ExtSoapOptions::defaults($wsdl, [])
                                ->withClassMap($this->getClassMaps())
        );

        $eventDispatcher = new EventDispatcher();

        return new $clientClass($engine, $eventDispatcher);
    }

    private function getClassMaps() {
        $classMapCollection = new ClassMapCollection();
        $classMapCollection->set(new ClassMap('generatePackagesNumbersV1', \radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV1Request::class));
        $classMapCollection->set(new ClassMap('openUMLFeV1', \radz2k\Dpd\Soap\Types\OpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('packageOpenUMLFeV1', \radz2k\Dpd\Soap\Types\PackageOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('parcelOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ParcelOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('packageAddressOpenUMLFeV1', \radz2k\Dpd\Soap\Types\PackageAddressOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('servicesOpenUMLFeV2', \radz2k\Dpd\Soap\Types\ServicesOpenUMLFeV2::class));
        $classMapCollection->set(new ClassMap('serviceCarryInOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceCarryInOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('serviceCODOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceCODOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('serviceCUDOpenUMLeFV1', \radz2k\Dpd\Soap\Types\ServiceCUDOpenUMLeFV1::class));
        $classMapCollection->set(new ClassMap('serviceDeclaredValueOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceDeclaredValueOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('serviceDedicatedDeliveryOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceDedicatedDeliveryOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('servicePalletOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServicePalletOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('serviceDutyOpenUMLeFV1', \radz2k\Dpd\Soap\Types\ServiceDutyOpenUMLeFV1::class));
        $classMapCollection->set(new ClassMap('serviceGuaranteeOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceGuaranteeOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('serviceInPersOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceInPersOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('servicePrivPersOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServicePrivPersOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('serviceRODOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceRODOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('serviceSelfColOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceSelfColOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('serviceTiresOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceTiresOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('serviceTiresExportOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceTiresExportOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('authDataV1', \radz2k\Dpd\Soap\Types\AuthDataV1::class));
        $classMapCollection->set(new ClassMap('generatePackagesNumbersV1Response', \radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV1Response::class));
        $classMapCollection->set(new ClassMap('packagesGenerationResponseV1', \radz2k\Dpd\Soap\Types\PackagesGenerationResponseV1::class));
        $classMapCollection->set(new ClassMap('sessionPGRV1', \radz2k\Dpd\Soap\Types\SessionPGRV1::class));
        $classMapCollection->set(new ClassMap('packagePGRV1', \radz2k\Dpd\Soap\Types\PackagePGRV1::class));
        $classMapCollection->set(new ClassMap('invalidFieldPGRV1', \radz2k\Dpd\Soap\Types\InvalidFieldPGRV1::class));
        $classMapCollection->set(new ClassMap('parcelPGRV1', \radz2k\Dpd\Soap\Types\ParcelPGRV1::class));
        $classMapCollection->set(new ClassMap('DPDServiceException', \radz2k\Dpd\Soap\Types\DPDServiceException::class));
        $classMapCollection->set(new ClassMap('packagesPickupCallV4', \radz2k\Dpd\Soap\Types\PackagesPickupCallV4Request::class));
        $classMapCollection->set(new ClassMap('dpdPickupCallParamsV3', \radz2k\Dpd\Soap\Types\DpdPickupCallParamsV3::class));
        $classMapCollection->set(new ClassMap('pickupCallSimplifiedDetailsDPPV1', \radz2k\Dpd\Soap\Types\PickupCallSimplifiedDetailsDPPV1::class));
        $classMapCollection->set(new ClassMap('pickupPackagesParamsDPPV1', \radz2k\Dpd\Soap\Types\PickupPackagesParamsDPPV1::class));
        $classMapCollection->set(new ClassMap('pickupCustomerDPPV1', \radz2k\Dpd\Soap\Types\PickupCustomerDPPV1::class));
        $classMapCollection->set(new ClassMap('pickupPayerDPPV1', \radz2k\Dpd\Soap\Types\PickupPayerDPPV1::class));
        $classMapCollection->set(new ClassMap('pickupSenderDPPV1', \radz2k\Dpd\Soap\Types\PickupSenderDPPV1::class));
        $classMapCollection->set(new ClassMap('packagesPickupCallV4Response', \radz2k\Dpd\Soap\Types\PackagesPickupCallV4Response::class));
        $classMapCollection->set(new ClassMap('packagesPickupCallResponseV3', \radz2k\Dpd\Soap\Types\PackagesPickupCallResponseV3::class));
        $classMapCollection->set(new ClassMap('statusInfoPCRV2', \radz2k\Dpd\Soap\Types\StatusInfoPCRV2::class));
        $classMapCollection->set(new ClassMap('errorDetailsPCRV2', \radz2k\Dpd\Soap\Types\ErrorDetailsPCRV2::class));
        $classMapCollection->set(new ClassMap('packagesPickupCallV3', \radz2k\Dpd\Soap\Types\PackagesPickupCallV3Request::class));
        $classMapCollection->set(new ClassMap('packagesPickupCallV3Response', \radz2k\Dpd\Soap\Types\PackagesPickupCallV3Response::class));
        $classMapCollection->set(new ClassMap('getCourierOrderAvailabilityV1', \radz2k\Dpd\Soap\Types\GetCourierOrderAvailabilityV1Request::class));
        $classMapCollection->set(new ClassMap('senderPlaceV1', \radz2k\Dpd\Soap\Types\SenderPlaceV1::class));
        $classMapCollection->set(new ClassMap('getCourierOrderAvailabilityV1Response', \radz2k\Dpd\Soap\Types\GetCourierOrderAvailabilityV1Response::class));
        $classMapCollection->set(new ClassMap('getCourierOrderAvailabilityResponseV1', \radz2k\Dpd\Soap\Types\GetCourierOrderAvailabilityResponseV1::class));
        $classMapCollection->set(new ClassMap('courierOrderAvailabilityRangeV1', \radz2k\Dpd\Soap\Types\CourierOrderAvailabilityRangeV1::class));
        $classMapCollection->set(new ClassMap('Exception', \radz2k\Dpd\Soap\Types\Exception::class));
        $classMapCollection->set(new ClassMap('packagesPickupCallV2', \radz2k\Dpd\Soap\Types\PackagesPickupCallV2Request::class));
        $classMapCollection->set(new ClassMap('dpdPickupCallParamsV2', \radz2k\Dpd\Soap\Types\DpdPickupCallParamsV2::class));
        $classMapCollection->set(new ClassMap('packagesPickupCallV2Response', \radz2k\Dpd\Soap\Types\PackagesPickupCallV2Response::class));
        $classMapCollection->set(new ClassMap('packagesPickupCallResponseV2', \radz2k\Dpd\Soap\Types\PackagesPickupCallResponseV2::class));
        $classMapCollection->set(new ClassMap('generatePackagesNumbersV4', \radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV4Request::class));
        $classMapCollection->set(new ClassMap('generateInternationalPackageNumbersV1', \radz2k\Dpd\Soap\Types\GenerateInternationalPackageNumbersV1Request::class));
        $classMapCollection->set(new ClassMap('openUMLFeV3', \radz2k\Dpd\Soap\Types\OpenUMLFeV3::class));
        $classMapCollection->set(new ClassMap('packageOpenUMLFeV3', \radz2k\Dpd\Soap\Types\PackageOpenUMLFeV3::class));
        $classMapCollection->set(new ClassMap('servicesOpenUMLFeV4', \radz2k\Dpd\Soap\Types\ServicesOpenUMLFeV4::class));
        $classMapCollection->set(new ClassMap('serviceFlagOpenUMLF', \radz2k\Dpd\Soap\Types\ServiceFlagOpenUMLF::class));
        $classMapCollection->set(new ClassMap('serviceDpdPickupOpenUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceDpdPickupOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('serviceDPDPudoReturnUMLFeV1', \radz2k\Dpd\Soap\Types\ServiceDPDPudoReturnUMLFeV1::class));
        $classMapCollection->set(new ClassMap('serviceDutyOpenUMLeFV2', \radz2k\Dpd\Soap\Types\ServiceDutyOpenUMLeFV2::class));
        $classMapCollection->set(new ClassMap('generatePackagesNumbersV4Response', \radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV4Response::class));
        $classMapCollection->set(new ClassMap('generateInternationalPackageNumbersV1Response', \radz2k\Dpd\Soap\Types\GenerateInternationalPackageNumbersV1Response::class));
        $classMapCollection->set(new ClassMap('packagesGenerationResponseV2', \radz2k\Dpd\Soap\Types\PackagesGenerationResponseV2::class));
        $classMapCollection->set(new ClassMap('sessionPGRV2', \radz2k\Dpd\Soap\Types\SessionPGRV2::class));
        $classMapCollection->set(new ClassMap('packagePGRV2', \radz2k\Dpd\Soap\Types\PackagePGRV2::class));
        $classMapCollection->set(new ClassMap('ValidationDetails', \radz2k\Dpd\Soap\Types\ValidationDetails::class));
        $classMapCollection->set(new ClassMap('validationInfoPGRV2', \radz2k\Dpd\Soap\Types\ValidationInfoPGRV2::class));
        $classMapCollection->set(new ClassMap('parcelPGRV2', \radz2k\Dpd\Soap\Types\ParcelPGRV2::class));
        $classMapCollection->set(new ClassMap('packagesPickupCallV1', \radz2k\Dpd\Soap\Types\PackagesPickupCallV1Request::class));
        $classMapCollection->set(new ClassMap('dpdPickupCallParamsV1', \radz2k\Dpd\Soap\Types\DpdPickupCallParamsV1::class));
        $classMapCollection->set(new ClassMap('contactInfoDPPV1', \radz2k\Dpd\Soap\Types\ContactInfoDPPV1::class));
        $classMapCollection->set(new ClassMap('pickupAddressDSPV1', \radz2k\Dpd\Soap\Types\PickupAddressDSPV1::class));
        $classMapCollection->set(new ClassMap('protocolDPPV1', \radz2k\Dpd\Soap\Types\ProtocolDPPV1::class));
        $classMapCollection->set(new ClassMap('packagesPickupCallV1Response', \radz2k\Dpd\Soap\Types\PackagesPickupCallV1Response::class));
        $classMapCollection->set(new ClassMap('packagesPickupCallResponseV1', \radz2k\Dpd\Soap\Types\PackagesPickupCallResponseV1::class));
        $classMapCollection->set(new ClassMap('protocolPCRV1', \radz2k\Dpd\Soap\Types\ProtocolPCRV1::class));
        $classMapCollection->set(new ClassMap('statusInfoPCRV1', \radz2k\Dpd\Soap\Types\StatusInfoPCRV1::class));
        $classMapCollection->set(new ClassMap('generatePackagesNumbersV2', \radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV2Request::class));
        $classMapCollection->set(new ClassMap('generatePackagesNumbersV2Response', \radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV2Response::class));
        $classMapCollection->set(new ClassMap('appendParcelsToPackageV1', \radz2k\Dpd\Soap\Types\AppendParcelsToPackageV1Request::class));
        $classMapCollection->set(new ClassMap('parcelsAppendV1', \radz2k\Dpd\Soap\Types\ParcelsAppendV1::class));
        $classMapCollection->set(new ClassMap('parcelsAppendSearchCriteriaPAV1', \radz2k\Dpd\Soap\Types\ParcelsAppendSearchCriteriaPAV1::class));
        $classMapCollection->set(new ClassMap('parcelAppendPAV1', \radz2k\Dpd\Soap\Types\ParcelAppendPAV1::class));
        $classMapCollection->set(new ClassMap('appendParcelsToPackageV1Response', \radz2k\Dpd\Soap\Types\AppendParcelsToPackageV1Response::class));
        $classMapCollection->set(new ClassMap('parcelsAppendResponseV1', \radz2k\Dpd\Soap\Types\ParcelsAppendResponseV1::class));
        $classMapCollection->set(new ClassMap('invalidFieldPAV1', \radz2k\Dpd\Soap\Types\InvalidFieldPAV1::class));
        $classMapCollection->set(new ClassMap('parcelsAppendParcelPAV1', \radz2k\Dpd\Soap\Types\ParcelsAppendParcelPAV1::class));
        $classMapCollection->set(new ClassMap('generatePackagesNumbersV3', \radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV3Request::class));
        $classMapCollection->set(new ClassMap('openUMLFeV2', \radz2k\Dpd\Soap\Types\OpenUMLFeV2::class));
        $classMapCollection->set(new ClassMap('packageOpenUMLFeV2', \radz2k\Dpd\Soap\Types\PackageOpenUMLFeV2::class));
        $classMapCollection->set(new ClassMap('servicesOpenUMLFeV3', \radz2k\Dpd\Soap\Types\ServicesOpenUMLFeV3::class));
        $classMapCollection->set(new ClassMap('generatePackagesNumbersV3Response', \radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV3Response::class));
        $classMapCollection->set(new ClassMap('importDeliveryBusinessEventV1', \radz2k\Dpd\Soap\Types\ImportDeliveryBusinessEventV1Request::class));
        $classMapCollection->set(new ClassMap('dpdParcelBusinessEventV1', \radz2k\Dpd\Soap\Types\DpdParcelBusinessEventV1::class));
        $classMapCollection->set(new ClassMap('dpdParcelBusinessEventDataV1', \radz2k\Dpd\Soap\Types\DpdParcelBusinessEventDataV1::class));
        $classMapCollection->set(new ClassMap('importDeliveryBusinessEventV1Response', \radz2k\Dpd\Soap\Types\ImportDeliveryBusinessEventV1Response::class));
        $classMapCollection->set(new ClassMap('importDeliveryBusinessEventResponseV1', \radz2k\Dpd\Soap\Types\ImportDeliveryBusinessEventResponseV1::class));
        $classMapCollection->set(new ClassMap('DeniedAccessWSException', \radz2k\Dpd\Soap\Types\DeniedAccessWSException::class));
        $classMapCollection->set(new ClassMap('SchemaValidationException', \radz2k\Dpd\Soap\Types\SchemaValidationException::class));
        $classMapCollection->set(new ClassMap('generateSpedLabelsV1', \radz2k\Dpd\Soap\Types\GenerateSpedLabelsV1Request::class));
        $classMapCollection->set(new ClassMap('dpdServicesParamsV1', \radz2k\Dpd\Soap\Types\DpdServicesParamsV1::class));
        $classMapCollection->set(new ClassMap('sessionDSPV1', \radz2k\Dpd\Soap\Types\SessionDSPV1::class));
        $classMapCollection->set(new ClassMap('packageDSPV1', \radz2k\Dpd\Soap\Types\PackageDSPV1::class));
        $classMapCollection->set(new ClassMap('parcelDSPV1', \radz2k\Dpd\Soap\Types\ParcelDSPV1::class));
        $classMapCollection->set(new ClassMap('generateSpedLabelsV1Response', \radz2k\Dpd\Soap\Types\GenerateSpedLabelsV1Response::class));
        $classMapCollection->set(new ClassMap('documentGenerationResponseV1', \radz2k\Dpd\Soap\Types\DocumentGenerationResponseV1::class));
        $classMapCollection->set(new ClassMap('sessionDGRV1', \radz2k\Dpd\Soap\Types\SessionDGRV1::class));
        $classMapCollection->set(new ClassMap('packageDGRV1', \radz2k\Dpd\Soap\Types\PackageDGRV1::class));
        $classMapCollection->set(new ClassMap('parcelDGRV1', \radz2k\Dpd\Soap\Types\ParcelDGRV1::class));
        $classMapCollection->set(new ClassMap('statusInfoDGRV1', \radz2k\Dpd\Soap\Types\StatusInfoDGRV1::class));
        $classMapCollection->set(new ClassMap('findPostalCodeV1', \radz2k\Dpd\Soap\Types\FindPostalCodeV1Request::class));
        $classMapCollection->set(new ClassMap('postalCodeV1', \radz2k\Dpd\Soap\Types\PostalCodeV1::class));
        $classMapCollection->set(new ClassMap('findPostalCodeV1Response', \radz2k\Dpd\Soap\Types\FindPostalCodeV1Response::class));
        $classMapCollection->set(new ClassMap('findPostalCodeResponseV1', \radz2k\Dpd\Soap\Types\FindPostalCodeResponseV1::class));
        $classMapCollection->set(new ClassMap('generateProtocolV1', \radz2k\Dpd\Soap\Types\GenerateProtocolV1Request::class));
        $classMapCollection->set(new ClassMap('generateProtocolV1Response', \radz2k\Dpd\Soap\Types\GenerateProtocolV1Response::class));
        $classMapCollection->set(new ClassMap('generateProtocolsWithDestinationsV2', \radz2k\Dpd\Soap\Types\GenerateProtocolsWithDestinationsV2Request::class));
        $classMapCollection->set(new ClassMap('dpdServicesParamsV2', \radz2k\Dpd\Soap\Types\DpdServicesParamsV2::class));
        $classMapCollection->set(new ClassMap('DeliveryDestinations', \radz2k\Dpd\Soap\Types\DeliveryDestinations::class));
        $classMapCollection->set(new ClassMap('sessionDSPV2', \radz2k\Dpd\Soap\Types\SessionDSPV2::class));
        $classMapCollection->set(new ClassMap('packageDSPV2', \radz2k\Dpd\Soap\Types\PackageDSPV2::class));
        $classMapCollection->set(new ClassMap('parcelDSPV2', \radz2k\Dpd\Soap\Types\ParcelDSPV2::class));
        $classMapCollection->set(new ClassMap('pickupAddressDSPV2', \radz2k\Dpd\Soap\Types\PickupAddressDSPV2::class));
        $classMapCollection->set(new ClassMap('deliveryDestination', \radz2k\Dpd\Soap\Types\DeliveryDestination::class));
        $classMapCollection->set(new ClassMap('DepotList', \radz2k\Dpd\Soap\Types\DepotList::class));
        $classMapCollection->set(new ClassMap('protocolDepot', \radz2k\Dpd\Soap\Types\ProtocolDepot::class));
        $classMapCollection->set(new ClassMap('generateProtocolsWithDestinationsV2Response', \radz2k\Dpd\Soap\Types\GenerateProtocolsWithDestinationsV2Response::class));
        $classMapCollection->set(new ClassMap('documentGenerationResponseV2', \radz2k\Dpd\Soap\Types\DocumentGenerationResponseV2::class));
        $classMapCollection->set(new ClassMap('DestinationDataList', \radz2k\Dpd\Soap\Types\DestinationDataList::class));
        $classMapCollection->set(new ClassMap('destinationsData', \radz2k\Dpd\Soap\Types\DestinationsData::class));
        $classMapCollection->set(new ClassMap('nonMatchingData', \radz2k\Dpd\Soap\Types\NonMatchingData::class));
        $classMapCollection->set(new ClassMap('sessionDGRV2', \radz2k\Dpd\Soap\Types\SessionDGRV2::class));
        $classMapCollection->set(new ClassMap('packageDGRV2', \radz2k\Dpd\Soap\Types\PackageDGRV2::class));
        $classMapCollection->set(new ClassMap('parcelDGRV2', \radz2k\Dpd\Soap\Types\ParcelDGRV2::class));
        $classMapCollection->set(new ClassMap('statusInfoDGRV2', \radz2k\Dpd\Soap\Types\StatusInfoDGRV2::class));
        $classMapCollection->set(new ClassMap('generateSpedLabelsV4', \radz2k\Dpd\Soap\Types\GenerateSpedLabelsV4Request::class));
        $classMapCollection->set(new ClassMap('generateSpedLabelsV4Response', \radz2k\Dpd\Soap\Types\GenerateSpedLabelsV4Response::class));
        $classMapCollection->set(new ClassMap('generateProtocolsWithDestinationsV1', \radz2k\Dpd\Soap\Types\GenerateProtocolsWithDestinationsV1Request::class));
        $classMapCollection->set(new ClassMap('generateProtocolsWithDestinationsV1Response', \radz2k\Dpd\Soap\Types\GenerateProtocolsWithDestinationsV1Response::class));
        $classMapCollection->set(new ClassMap('generateProtocolV2', \radz2k\Dpd\Soap\Types\GenerateProtocolV2Request::class));
        $classMapCollection->set(new ClassMap('generateProtocolV2Response', \radz2k\Dpd\Soap\Types\GenerateProtocolV2Response::class));
        $classMapCollection->set(new ClassMap('generateSpedLabelsV3', \radz2k\Dpd\Soap\Types\GenerateSpedLabelsV3Request::class));
        $classMapCollection->set(new ClassMap('generateSpedLabelsV3Response', \radz2k\Dpd\Soap\Types\GenerateSpedLabelsV3Response::class));
        $classMapCollection->set(new ClassMap('generateSpedLabelsV2', \radz2k\Dpd\Soap\Types\GenerateSpedLabelsV2Request::class));
        $classMapCollection->set(new ClassMap('generateSpedLabelsV2Response', \radz2k\Dpd\Soap\Types\GenerateSpedLabelsV2Response::class));
        $classMapCollection->set(new ClassMap('importPackagesXV1', \radz2k\Dpd\Soap\Types\ImportPackagesXV1Request::class));
        $classMapCollection->set(new ClassMap('importPackagesXV1Response', \radz2k\Dpd\Soap\Types\ImportPackagesXV1Response::class));
        $classMapCollection->set(new ClassMap('internationalPackageOpenUMLFeV1', \radz2k\Dpd\Soap\Types\InternationalPackageOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('internationalParcelOpenUMLFeV1', \radz2k\Dpd\Soap\Types\InternationalParcelOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('internationalServicesOpenUMLFeV1', \radz2k\Dpd\Soap\Types\InternationalServicesOpenUMLFeV1::class));
        $classMapCollection->set(new ClassMap('getEventsForCustomerV4', \radz2k\Dpd\Soap\Types\GetEventsForCustomerV4Request::class));
        $classMapCollection->set(new ClassMap('getEventsForCustomerV4Response', \radz2k\Dpd\Soap\Types\GetEventsForCustomerV4Response::class));
        $classMapCollection->set(new ClassMap('customerEventsResponseV2', \radz2k\Dpd\Soap\Types\CustomerEventsResponseV2::class));
        $classMapCollection->set(new ClassMap('customerEventV2', \radz2k\Dpd\Soap\Types\CustomerEventV2::class));
        $classMapCollection->set(new ClassMap('customerEventDataV2', \radz2k\Dpd\Soap\Types\CustomerEventDataV2::class));
        $classMapCollection->set(new ClassMap('getEventsForCustomerV3', \radz2k\Dpd\Soap\Types\GetEventsForCustomerV3Request::class));
        $classMapCollection->set(new ClassMap('getEventsForCustomerV3Response', \radz2k\Dpd\Soap\Types\GetEventsForCustomerV3Response::class));
        $classMapCollection->set(new ClassMap('getEventsForCustomerV2', \radz2k\Dpd\Soap\Types\GetEventsForCustomerV2Request::class));
        $classMapCollection->set(new ClassMap('getEventsForCustomerV2Response', \radz2k\Dpd\Soap\Types\GetEventsForCustomerV2Response::class));
        $classMapCollection->set(new ClassMap('customerEventsResponseV1', \radz2k\Dpd\Soap\Types\CustomerEventsResponseV1::class));
        $classMapCollection->set(new ClassMap('customerEventV1', \radz2k\Dpd\Soap\Types\CustomerEventV1::class));
        $classMapCollection->set(new ClassMap('getEventsForCustomerV1', \radz2k\Dpd\Soap\Types\GetEventsForCustomerV1Request::class));
        $classMapCollection->set(new ClassMap('getEventsForCustomerV1Response', \radz2k\Dpd\Soap\Types\GetEventsForCustomerV1Response::class));
        $classMapCollection->set(new ClassMap('getEventsForWaybillV1', \radz2k\Dpd\Soap\Types\GetEventsForWaybillV1Request::class));
        $classMapCollection->set(new ClassMap('getEventsForWaybillV1Response', \radz2k\Dpd\Soap\Types\GetEventsForWaybillV1Response::class));
        $classMapCollection->set(new ClassMap('customerEventsResponseV3', \radz2k\Dpd\Soap\Types\CustomerEventsResponseV3::class));
        $classMapCollection->set(new ClassMap('customerEventV3', \radz2k\Dpd\Soap\Types\CustomerEventV3::class));
        $classMapCollection->set(new ClassMap('customerEventDataV3', \radz2k\Dpd\Soap\Types\CustomerEventDataV3::class));
        $classMapCollection->set(new ClassMap('markEventsAsProcessedV1Response', \radz2k\Dpd\Soap\Types\MarkEventsAsProcessedV1Response::class));
        return $classMapCollection;
    }

    private function getAuthDataStruct(): AuthDataV1 {
        $authData = new AuthDataV1();
        $authData->setLogin($this->login);
        $authData->setPassword($this->password);
        $authData->setMasterFid($this->masterFid);

        return $authData;
    }

    /**
     * @param FindPostalCodeRequest $request
     *
     * @return FindPostalCodeResponse
     */
    public function findPostalCode(FindPostalCodeRequest $request): FindPostalCodeResponse {
        $payload = $request->toPayload();
        $payload->setAuthData($this->getAuthDataStruct());
        $response = $this->obtainPackageServiceClient()->findPostalCodeV1($payload);

        return FindPostalCodeResponse::from($response);
    }

    /**
     * @param GeneratePackageNumbersRequest $request
     *
     * @return GeneratePackageNumbersResponse
     */
    public function generatePackageNumbers(GeneratePackageNumbersRequest $request): GeneratePackageNumbersResponse {
        $payload = $request->toPayload();
        $payload->setAuthData($this->getAuthDataStruct());
        $response = $this->obtainPackageServiceClient()->generatePackagesNumbersV4($payload);
        
        Debugbar::addMessage($response);
        
        return GeneratePackageNumbersResponse::from($response);
    }

    /**
     * @param GenerateInternationalPackageNumbersRequest $request
     *
     * @return GeneratePackageNumbersResponse
     */
    public function generateInternationalPackageNumbers(GenerateInternationalPackageNumbersRequest $request): GenerateInternationalPackageNumbersResponse {
        $payload = $request->toPayload();
        $payload->setAuthData($this->getAuthDataStruct());
        $response = $this->obtainPackageServiceClient()->generateInternationalPackageNumbersV1($payload);
        
        Debugbar::addMessage($response);
        
        return GenerateInternationalPackageNumbersResponse::from($response);
    }
    
    /**
     * @param GenerateLabelsRequest $request
     *
     * @return GenerateLabelsResponse
     */
    public function generateLabels(GenerateLabelsRequest $request): GenerateLabelsResponse {
        $payload = $request->toPayload();
        $payload->setAuthData($this->getAuthDataStruct());
        $response = $this->obtainPackageServiceClient()->generateSpedLabelsV1($payload);

        return GenerateLabelsResponse::from($response);
    }

    /**
     * @param GenerateProtocolRequest $request
     *
     * @return GenerateProtocolResponse
     */
    public function generateProtocol(GenerateProtocolRequest $request): GenerateProtocolResponse {
        $payload = $request->toPayload();
        $payload->setAuthData($this->getAuthDataStruct());
        $response = $this->obtainPackageServiceClient()->generateProtocolV2($payload);

        return GenerateProtocolResponse::from($response);
    }

    /**
     * @param GetCourierAvailabilityRequest $request
     *
     * @return GetCourierAvailabilityResponse
     */
    public function getCourierAvailability(GetCourierAvailabilityRequest $request): GetCourierAvailabilityResponse {
        $payload = $request->toPayload();
        $payload->setAuthData($this->getAuthDataStruct());
        $response = $this->obtainPackageServiceClient()->getCourierOrderAvailabilityV1($payload);

        return GetCourierAvailabilityResponse::from($response);
    }

    /**
     * @param CollectionOrderRequest $request
     *
     * @return CollectionOrderResponse
     */
    public function collectionOrder(CollectionOrderRequest $request): CollectionOrderResponse {
        $payload = $request->toPayload();
        $payload->setAuthDataV1($this->getAuthDataStruct());
        $response = $this->obtainAppServiceClient()->importPackagesXV1($payload);

        return CollectionOrderResponse::from($response);
    }

    /**
     * @param GetParcelTrackingRequest $request
     *
     * @return GetParcelTrackingResponse
     */
    public function getParcelTracking(GetParcelTrackingRequest $request): GetParcelTrackingResponse {
        $payload = $request->toPayload();
        $payload->setAuthData($this->getAuthDataStruct());
        $response = $this->obtainInfoServiceClient()->getEventsForWaybillV1($payload);
        return GetParcelTrackingResponse::from($response);
    }

}
