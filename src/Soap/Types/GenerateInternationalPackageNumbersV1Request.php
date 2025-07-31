<?php

namespace radz2k\Dpd\Soap\Types;

use Phpro\SoapClient\Type\RequestInterface;

class GenerateInternationalPackageNumbersV1Request implements RequestInterface
{
    /**
     * @var OpenUMLFeV3
     */
    private $internationalOpenUMLFeV1;

    /**
     * @var AuthDataV1
     */
    private $authDataV1;

    /**
     * @return OpenUMLFeV3
     */
    public function getOpenUMLFe() : OpenUMLFeV3
    {
        return $this->internationalOpenUMLFeV1;
    }

    /**
     * @param OpenUMLFeV3 $openUMLFeV3
     *
     * @return GenerateInternationalPackageNumbersV1Request
     */
    public function setOpenUMLFe(OpenUMLFeV3 $openUMLFeV3) : GenerateInternationalPackageNumbersV1Request
    {
        $this->internationalOpenUMLFeV1 = $openUMLFeV3;

        return $this;
    }

    /**
     * @return AuthDataV1
     */
    public function getAuthData() : ?AuthDataV1
    {
        return $this->authDataV1;
    }

    /**
     * @param AuthDataV1 $authDataV1
     *
     * @return GenerateInternationalPackageNumbersV1Request
     */
    public function setAuthData(AuthDataV1 $authDataV1) : GenerateInternationalPackageNumbersV1Request
    {
        $this->authDataV1 = $authDataV1;

        return $this;
    }
}
