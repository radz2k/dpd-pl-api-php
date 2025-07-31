<?php

namespace radz2k\Dpd\Request;

use radz2k\Dpd\Objects\Package;
use radz2k\Dpd\Objects\Parcel;
use radz2k\Dpd\Objects\Enum\PayerType;
use radz2k\Dpd\Objects\Receiver;
use radz2k\Dpd\Objects\Sender;
use radz2k\Dpd\Objects\Services;
use radz2k\Dpd\Soap\Types\GenerateInternationalPackageNumbersV1Request;
use radz2k\Dpd\Soap\Types\OpenUMLFeV3;
use radz2k\Dpd\Soap\Types\PackageAddressOpenUMLFeV1;
use radz2k\Dpd\Soap\Types\InternationalPackageOpenUMLFeV1;
use radz2k\Dpd\Soap\Types\InternationalParcelOpenUMLFeV1;
use radz2k\Dpd\Soap\Types\PayerTypeEnumOpenUMLFeV1;
use radz2k\Dpd\Soap\Types\ServiceDPDPudoReturnUMLFeV1;
use radz2k\Dpd\Soap\Types\InternationalServicesOpenUMLFeV1;
use radz2k\Dpd\StringHelper\StringHelper;

class GenerateInternationalPackageNumbersRequest {

    /**
     * @var Package[]
     */
    private $packages;

    /**
     * GenerateInternationalPackageNumbersRequest constructor.
     *
     * @param $packages
     */
    protected function __construct(array $packages) {
        $this->packages = $packages;
    }

    /**
     * @param Package $package
     *
     * @return GenerateInternationalPackageNumbersRequest
     */
    public static function fromPackage(Package $package): GenerateInternationalPackageNumbersRequest {
        return new static([$package]);
    }

    /**
     * @param Package[] $packages
     *
     * @return GenerateInternationalPackageNumbersRequest
     */
    public static function fromPackages(array $packages): GenerateInternationalPackageNumbersRequest {
        return new static($packages);
    }

    /**
     * @return GenerateInternationalPackageNumbersV1Request
     */
    public function toPayload(): GenerateInternationalPackageNumbersV1Request {
        $request = new GenerateInternationalPackageNumbersV1Request();
        $openUMLFeV3 = new OpenUMLFeV3();
        $openUMLFeV3->setPackages($this->generatePackageObject($this->packages));
        $request->setOpenUMLFe($openUMLFeV3);

        return $request;
    }

    /**
     * @param Package[] $packages
     *
     * @return InternationalPackageOpenUMLFeV1[]
     */
    private function generatePackageObject(array $packages): array {
        $packageObjects = [];
        foreach ($packages as $package) {
            $packageObject = new InternationalPackageOpenUMLFeV1();
            $packageObject->setParcels($this->generateParcelsObject($package->getParcels()));
            $packageObject->setSender($this->generateSenderObject($package->getSender()));
            $packageObject->setReceiver($this->generateReceiverObject($package->getReceiver()));
            $packageObject->setPayerType($this->generatePayerTypeObject($package->getPayerType()));
            $packageObject->setServices($this->generateServicesObject($package->getServices()));
            $packageObject->setThirdPartyFID($package->getThirdPartyFid());
            $packageObject->setRef1($package->getRef1());
            $packageObject->setRef2($package->getRef2());
            $packageObject->setRef3($package->getRef3());
            $packageObjects[] = $packageObject;
        }

        return $packageObjects;
    }

    /**
     * @param Parcel[] $parcels
     *
     * @return InternationalParcelOpenUMLFeV1[]
     */
    private function generateParcelsObject(array $parcels): array {
        $parcelObjects = [];
        foreach ($parcels as $parcel) {
            $parcelObject = new InternationalParcelOpenUMLFeV1();
            $parcelObject->setSizeX($parcel->getSizeX());
            $parcelObject->setSizeY($parcel->getSizeY());
            $parcelObject->setSizeZ($parcel->getSizeZ());
            $parcelObject->setWeight($parcel->getWeight());
            $parcelObject->setContent($parcel->getContents());
            $parcelObject->setReference($parcel->getReference());
            [$customerData1, $customerData2, $customerData3] = StringHelper::mb_str_split(str_pad((string) $parcel->getCustomerNotes(), 195, ' '), 65);
            $parcelObject->setCustomerData1(empty(trim($customerData1)) ? null : trim($customerData1));
            $parcelObject->setCustomerData2(empty(trim($customerData2)) ? null : trim($customerData2));
            $parcelObject->setCustomerData3(empty(trim($customerData3)) ? null : trim($customerData3));
            $parcelObjects[] = $parcelObject;
        }

        return $parcelObjects;
    }

    /**
     * @param Sender $sender
     *
     * @return PackageAddressOpenUMLFeV1
     */
    private function generateSenderObject(Sender $sender): PackageAddressOpenUMLFeV1 {
        $address = new PackageAddressOpenUMLFeV1();
        $address->setName($sender->getName());
        $address->setAddress($sender->getAddress());
        $address->setFid($sender->getFid());
        $address->setPhone($sender->getPhone());
        $address->setPostalCode($sender->getPostalCode());
        $address->setCity($sender->getCity());
        $address->setCountryCode($sender->getCountryCode());
        $address->setCompany($sender->getCompany());
        $address->setEmail($sender->getEmail());

        return $address;
    }

    /**
     * @param Receiver $receiver
     *
     * @return PackageAddressOpenUMLFeV1
     */
    private function generateReceiverObject(Receiver $receiver): PackageAddressOpenUMLFeV1 {
        $address = new PackageAddressOpenUMLFeV1();
        $address->setName($receiver->getName());
        $address->setAddress($receiver->getAddress());
        $address->setPhone($receiver->getPhone());
        $address->setPostalCode($receiver->getPostalCode());
        $address->setCity($receiver->getCity());
        $address->setCountryCode($receiver->getCountryCode());
        $address->setCompany($receiver->getCompany());
        $address->setEmail($receiver->getEmail());

        return $address;
    }

    /**
     * @param PayerType $payerType
     *
     * @return PayerTypeEnumOpenUMLFeV1
     */
    private function generatePayerTypeObject(PayerType $payerType): PayerTypeEnumOpenUMLFeV1 {
        return new PayerTypeEnumOpenUMLFeV1((string) $payerType);
    }

    /**
     * @param Services $services
     *
     * @return InternationalServicesOpenUMLFeV1
     */
    private function generateServicesObject(Services $services): InternationalServicesOpenUMLFeV1     {
        $servicesObject = new InternationalServicesOpenUMLFeV1();
        $servicesObject->setPudoReturn(new ServiceDPDPudoReturnUMLFeV1());
        return $servicesObject;
    }
}
