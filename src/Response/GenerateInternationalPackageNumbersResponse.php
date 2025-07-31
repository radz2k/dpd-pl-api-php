<?php

namespace radz2k\Dpd\Response;

use radz2k\Dpd\Objects\RegisteredPackage;
use radz2k\Dpd\Objects\RegisteredParcel;
use radz2k\Dpd\Soap\Types\GenerateInternationalPackageNumbersV1Response;
use radz2k\Dpd\Soap\Types\PackagePGRV2;
use radz2k\Dpd\Soap\Types\ParcelPGRV2;

class GenerateInternationalPackageNumbersResponse
{
    private $packages;

    /**
     * GenerateInternationalPackageNumbersResponse constructor.
     *
     * @param RegisteredPackage[] $packages
     */
    protected function __construct(array $packages)
    {
        $this->packages = $packages;
    }

    /**
     * @param GenerateInternationalPackageNumbersV1Response $response
     *
     * @throws \Exception
     *
     * @return GenerateInternationalPackageNumbersResponse
     */
    public static function from(GenerateInternationalPackageNumbersV1Response $response)
    {
        // Package validation info exception handler
        if ('OK' !== $response->getReturn()->getStatus() && null !== $response->getReturn()->getPackages() && is_array($response->getReturn()->getPackages()->Package)) {
            $packages = $response->getReturn()->getPackages()->Package;
            foreach ($packages as $package) {
                if (null !== $package->getValidationDetails() && is_array($package->getValidationDetails()->ValidationInfo)) {
                    $packageValidationInfo = $package->getValidationDetails()->ValidationInfo[0]->Info;
                    $packageValidationErrorId = $package->getValidationDetails()->ValidationInfo[0]->ErrorId;
                    throw new \Exception($packageValidationInfo, $packageValidationErrorId);
                }
            }
            throw new \Exception("Something went wrong.", $response->getReturn()->getStatus());
        }
        
        if ('OK' !== $response->getReturn()->getStatus()) {
            throw new \Exception($response->getReturn()->getStatus());
        }

        if (null !== $response->getReturn()->getPackages() && is_array($response->getReturn()->getPackages()->Package)) {
            $packages = $response->getReturn()->getPackages()->Package;
            $registeredPackages = [];

            /** @var PackagePGRV2 $package */
            foreach ($packages as $package) {
                $packageValidationDetails = [];
                if (null !== $package->getValidationDetails() && is_array($package->getValidationDetails()->ValidationInfo)) {
                    $packageValidationDetails = $package->getValidationDetails()->ValidationInfo;
                }

                $parcels = [];
                if (null !== $package->getParcels() && is_array($package->getParcels()->Parcel)) {
                    $parcels = $package->getParcels()->Parcel;
                }

                $registeredParcels = [];
                /** @var ParcelPGRV2 $parcel */
                foreach ($parcels as $parcel) {
                    $parcelValidationDetails = [];
                    if (null !== $parcel->getValidationDetails() && is_array($parcel->getValidationDetails()->ValidationInfo)) {
                        $parcelValidationDetails = $parcel->getValidationDetails()->ValidationInfo;
                    }

                    $registeredParcels[] = new RegisteredParcel(
                        $parcel->getParcelId(),
                        $parcel->getStatus(),
                        $parcel->getReference(),
                        $parcelValidationDetails,
                        $parcel->getWaybill()
                    );
                }

                $registeredPackages[] = new RegisteredPackage(
                    $package->getPackageId(),
                    $package->getStatus(),
                    $package->getReference(),
                    $packageValidationDetails,
                    $registeredParcels
                );
            }

            return new static($registeredPackages);
        }
    }

    /**
     * @return RegisteredPackage[]
     */
    public function getPackages(): array
    {
        return $this->packages;
    }
}
