<?php

namespace radz2k\Dpd\Response\Deserializer;

use Sabre\Xml\Reader;
use Sabre\Xml\Service;
use radz2k\Dpd\Objects\CollectionOrderedPackage;
use radz2k\Dpd\Objects\CollectionOrderedParcel;

class XmlCollectionOrderResponseDeserializer {

    private $xmlEngine;

    public function __construct() {
        $this->xmlEngine = new Service();
        $this->xmlEngine->elementMap = [
            'PackagesImportResponse' => function (Reader $reader) {
                $values = \Sabre\Xml\Deserializer\keyValue($reader);
                return $values['{}Packages'];
            },
            'Packages' => function (Reader $reader) {
                return \Sabre\Xml\Deserializer\repeatingElements($reader, '{}Package');
            },
            'Package' => function (Reader $reader) {
                $values = \Sabre\Xml\Deserializer\keyValue($reader);

                $parcels = [];
                foreach ($values['{}Parcels'] as $parcel) {
                    $parcels[] = $parcel['value'];
                }
                return new CollectionOrderedPackage(
                $values['{}PackageId'],
                $values['{}Reference'],
                $parcels,
                $values['{}StatusInfo'][0]['value'],
                $values['{}OrderNumber']
                );
            },
            'Parcel' => function (Reader $reader) {
                $values = \Sabre\Xml\Deserializer\keyValue($reader);
                return new CollectionOrderedParcel(
                $values['{}ParcelId'],
                $values['{}Waybill'] ?? $values['{}ParcelId']  // Workaround because for CROUT waybill is not returned.
                );
            }
        ];
    }

    public function deserialize(string $xml) {
        return $this->xmlEngine->parse($xml);
    }
}
