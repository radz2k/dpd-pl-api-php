<?php

namespace radz2k\Dpd\Soap\Types;

class InternationalPackageOpenUMLFeV1
{

    /**
     * @var InternationalParcelOpenUMLFeV1[]
     */
    private $parcels;

    /**
     * @var PayerTypeEnumOpenUMLFeV1
     */
    private $payerType;

    /**
     * @var PackageAddressOpenUMLFeV1
     */
    private $receiver;

    /**
     * @var string
     */
    private $ref1;

    /**
     * @var string
     */
    private $ref2;

    /**
     * @var string
     */
    private $ref3;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var PackageAddressOpenUMLFeV1
     */
    private $sender;

    /**
     * @var InternationalServicesOpenUMLFeV1
     */
    private $services;

    /**
     * @var int
     */
    private $thirdPartyFID;

    /**
     * @return InternationalParcelOpenUMLFeV1[]
     */
    public function getParcels() : array
    {
        return $this->parcels;
    }

    /**
     * @param InternationalParcelOpenUMLFeV1[] $parcels
     * @return InternationalPackageOpenUMLFeV1
     */
    public function setParcels(array $parcels) : InternationalPackageOpenUMLFeV1
    {
        $this->parcels = $parcels;
        return $this;
    }

    /**
     * @return PayerTypeEnumOpenUMLFeV1
     */
    public function getPayerType() : PayerTypeEnumOpenUMLFeV1
    {
        return $this->payerType;
    }

    /**
     * @param PayerTypeEnumOpenUMLFeV1 $payerType
     * @return InternationalPackageOpenUMLFeV1
     */
    public function setPayerType(PayerTypeEnumOpenUMLFeV1 $payerType) : InternationalPackageOpenUMLFeV1
    {
        $this->payerType = $payerType;
        return $this;
    }

    /**
     * @return PackageAddressOpenUMLFeV1
     */
    public function getReceiver() : PackageAddressOpenUMLFeV1
    {
        return $this->receiver;
    }

    /**
     * @param PackageAddressOpenUMLFeV1 $receiver
     * @return InternationalPackageOpenUMLFeV1
     */
    public function setReceiver(PackageAddressOpenUMLFeV1 $receiver) : InternationalPackageOpenUMLFeV1
    {
        $this->receiver = $receiver;
        return $this;
    }

    /**
     * @return string
     */
    public function getRef1() : ?string
    {
        return $this->ref1;
    }

    /**
     * @param string $ref1
     * @return InternationalPackageOpenUMLFeV1
     */
    public function setRef1(?string $ref1) : InternationalPackageOpenUMLFeV1
    {
        $this->ref1 = $ref1;
        return $this;
    }

    /**
     * @return string
     */
    public function getRef2() : ?string
    {
        return $this->ref2;
    }

    /**
     * @param string $ref2
     * @return InternationalPackageOpenUMLFeV1
     */
    public function setRef2(?string $ref2) : InternationalPackageOpenUMLFeV1
    {
        $this->ref2 = $ref2;
        return $this;
    }

    /**
     * @return string
     */
    public function getRef3() : ?string
    {
        return $this->ref3;
    }

    /**
     * @param string $ref3
     * @return InternationalPackageOpenUMLFeV1
     */
    public function setRef3(?string $ref3) : InternationalPackageOpenUMLFeV1
    {
        $this->ref3 = $ref3;
        return $this;
    }

    /**
     * @return string
     */
    public function getReference() : ?string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     * @return InternationalPackageOpenUMLFeV1
     */
    public function setReference(string $reference) : InternationalPackageOpenUMLFeV1
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return PackageAddressOpenUMLFeV1
     */
    public function getSender() : ?PackageAddressOpenUMLFeV1
    {
        return $this->sender;
    }

    /**
     * @param PackageAddressOpenUMLFeV1 $sender
     * @return InternationalPackageOpenUMLFeV1
     */
    public function setSender(PackageAddressOpenUMLFeV1 $sender) : InternationalPackageOpenUMLFeV1
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @return InternationalServicesOpenUMLFeV1
     */
    public function getServices() : ?InternationalServicesOpenUMLFeV1
    {
        return $this->services;
    }

    /**
     * @param InternationalServicesOpenUMLFeV1 $services
     * @return InternationalPackageOpenUMLFeV1
     */
    public function setServices(InternationalServicesOpenUMLFeV1 $services) : InternationalPackageOpenUMLFeV1
    {
        $this->services = $services;
        return $this;
    }

    /**
     * @return int
     */
    public function getThirdPartyFID() : ?int
    {
        return $this->thirdPartyFID;
    }

    /**
     * @param int $thirdPartyFID
     * @return InternationalPackageOpenUMLFeV1
     */
    public function setThirdPartyFID(?int $thirdPartyFID) : InternationalPackageOpenUMLFeV1
    {
        $this->thirdPartyFID = $thirdPartyFID;
        return $this;
    }


}

