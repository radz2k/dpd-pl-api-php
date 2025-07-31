<?php

namespace radz2k\Dpd\Soap\Client;

use Phpro\SoapClient\Caller\Caller;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Exception\SoapException;
use Soap\Engine\Engine;
//use Debugbar;

class PackageServicesClient implements Caller
{
    private Engine $engine;

    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    public function __invoke(string $method, RequestInterface $request): ResultInterface
    {
        try {
            
            // Debugbar (request)
            //Debugbar::addMessage($request);
   
            $arguments = ($request instanceof MultiArgumentRequestInterface) ? $request->getArguments() : [$request];
            $result = $this->engine->request($method, $arguments);

            // Debugbar (response)
            //Debugbar::addMessage($result);

            if ($result instanceof ResultProviderInterface) {
                $result = $result->getResult();
            }

            if (!$result instanceof ResultInterface) {
                $result = new MixedResult($result);
            }
        } catch (\Exception $exception) {
            throw SoapException::fromThrowable($exception);
        }

        return $result;
    }

    public function add(Add $parameters): AddResponse {
        return ($this->caller)('Add', $parameters);
    }
    
    //    public function appendParcelsToPackageV1(\radz2k\Dpd\Soap\Types\AppendParcelsToPackageV1Request $appendParcelsToPackageV1) : \radz2k\Dpd\Soap\Types\appendParcelsToPackageV1Response
//    {
//        return $this->call('appendParcelsToPackageV1', $appendParcelsToPackageV1);
//    }

    public function findPostalCodeV1(\radz2k\Dpd\Soap\Types\FindPostalCodeV1Request $findPostalCodeV1) : \radz2k\Dpd\Soap\Types\findPostalCodeV1Response
    {
        return $this->__invoke('findPostalCodeV1', $findPostalCodeV1);
    }

//    public function generatePackagesNumbersV1(\radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV1Request $generatePackagesNumbersV1) : \radz2k\Dpd\Soap\Types\generatePackagesNumbersV1Response
//    {
//        return $this->call('generatePackagesNumbersV1', $generatePackagesNumbersV1);
//    }

//    public function generatePackagesNumbersV2(\radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV2Request $generatePackagesNumbersV2) : \radz2k\Dpd\Soap\Types\generatePackagesNumbersV2Response
//    {
//        return $this->call('generatePackagesNumbersV2', $generatePackagesNumbersV2);
//    }

//    public function generatePackagesNumbersV3(\radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV3Request $generatePackagesNumbersV3) : \radz2k\Dpd\Soap\Types\generatePackagesNumbersV3Response
//    {
//        return $this->call('generatePackagesNumbersV3', $generatePackagesNumbersV3);
//    }

    public function generatePackagesNumbersV4(\radz2k\Dpd\Soap\Types\GeneratePackagesNumbersV4Request $generatePackagesNumbersV4) : \radz2k\Dpd\Soap\Types\generatePackagesNumbersV4Response
    {
        return $this->__invoke('generatePackagesNumbersV4', $generatePackagesNumbersV4);
    }
    
    public function generateInternationalPackageNumbersV1(\radz2k\Dpd\Soap\Types\GenerateInternationalPackageNumbersV1Request $generateInternationalPackageNumbersV1) : \radz2k\Dpd\Soap\Types\generateInternationalPackageNumbersV1Response
    {
        return $this->__invoke('generateInternationalPackageNumbersV1', $generateInternationalPackageNumbersV1);
    }

//    public function generateProtocolV1(\radz2k\Dpd\Soap\Types\GenerateProtocolV1Request $generateProtocolV1) : \radz2k\Dpd\Soap\Types\generateProtocolV1Response
//    {
//        return $this->call('generateProtocolV1', $generateProtocolV1);
//    }

//    public function generateProtocolsWithDestinationsV1(\radz2k\Dpd\Soap\Types\GenerateProtocolsWithDestinationsV1Request $generateProtocolsWithDestinationsV1) : \radz2k\Dpd\Soap\Types\generateProtocolsWithDestinationsV1Response
//    {
//        return $this->call('generateProtocolsWithDestinationsV1', $generateProtocolsWithDestinationsV1);
//    }

    public function generateProtocolV2(\radz2k\Dpd\Soap\Types\GenerateProtocolV2Request $generateProtocolV2) : \radz2k\Dpd\Soap\Types\generateProtocolV2Response
    {
        return $this->__invoke('generateProtocolV2', $generateProtocolV2);
    }

//    public function generateProtocolsWithDestinationsV2(\radz2k\Dpd\Soap\Types\GenerateProtocolsWithDestinationsV2Request $generateProtocolsWithDestinationsV2) : \radz2k\Dpd\Soap\Types\generateProtocolsWithDestinationsV2Response
//    {
//        return $this->call('generateProtocolsWithDestinationsV2', $generateProtocolsWithDestinationsV2);
//    }

    public function generateSpedLabelsV1(\radz2k\Dpd\Soap\Types\GenerateSpedLabelsV1Request $generateSpedLabelsV1) : \radz2k\Dpd\Soap\Types\generateSpedLabelsV1Response
    {
        return $this->__invoke('generateSpedLabelsV1', $generateSpedLabelsV1);
    }

//    public function generateSpedLabelsV2(\radz2k\Dpd\Soap\Types\GenerateSpedLabelsV2Request $generateSpedLabelsV2) : \radz2k\Dpd\Soap\Types\generateSpedLabelsV2Response
//    {
//        return $this->call('generateSpedLabelsV2', $generateSpedLabelsV2);
//    }

//    public function generateSpedLabelsV3(\radz2k\Dpd\Soap\Types\GenerateSpedLabelsV3Request $generateSpedLabelsV3) : \radz2k\Dpd\Soap\Types\generateSpedLabelsV3Response
//    {
//        return $this->call('generateSpedLabelsV3', $generateSpedLabelsV3);
//    }

//    public function generateSpedLabelsV4(\radz2k\Dpd\Soap\Types\GenerateSpedLabelsV4Request $generateSpedLabelsV4) : \radz2k\Dpd\Soap\Types\generateSpedLabelsV4Response
//    {
//        return $this->call('generateSpedLabelsV4', $generateSpedLabelsV4);
//    }

    public function packagesPickupCallV1(\radz2k\Dpd\Soap\Types\PackagesPickupCallV1Request $packagesPickupCallV1) : \radz2k\Dpd\Soap\Types\packagesPickupCallV1Response
    {
        return $this->__invoke('packagesPickupCallV1', $packagesPickupCallV1);
    }

//    public function packagesPickupCallV2(\radz2k\Dpd\Soap\Types\PackagesPickupCallV2Request $packagesPickupCallV2) : \radz2k\Dpd\Soap\Types\packagesPickupCallV2Response
//    {
//        return $this->call('packagesPickupCallV2', $packagesPickupCallV2);
//    }

//    public function packagesPickupCallV3(\radz2k\Dpd\Soap\Types\PackagesPickupCallV3Request $packagesPickupCallV3) : \radz2k\Dpd\Soap\Types\packagesPickupCallV3Response
//    {
//        return $this->call('packagesPickupCallV3', $packagesPickupCallV3);
//    }

    public function getCourierOrderAvailabilityV1(\radz2k\Dpd\Soap\Types\GetCourierOrderAvailabilityV1Request $getCourierOrderAvailabilityV1) : \radz2k\Dpd\Soap\Types\getCourierOrderAvailabilityV1Response
    {
        return $this->__invoke('getCourierOrderAvailabilityV1', $getCourierOrderAvailabilityV1);
    }

//    public function packagesPickupCallV4(\radz2k\Dpd\Soap\Types\PackagesPickupCallV4Request $packagesPickupCallV4) : \radz2k\Dpd\Soap\Types\packagesPickupCallV4Response
//    {
//        return $this->call('packagesPickupCallV4', $packagesPickupCallV4);
//    }

//    public function importDeliveryBusinessEventV1(\radz2k\Dpd\Soap\Types\ImportDeliveryBusinessEventV1Request $importDeliveryBusinessEventV1) : \radz2k\Dpd\Soap\Types\importDeliveryBusinessEventV1Response
//    {
//        return $this->call('importDeliveryBusinessEventV1', $importDeliveryBusinessEventV1);
//    }
}
