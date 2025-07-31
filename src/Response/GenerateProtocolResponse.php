<?php

namespace radz2k\Dpd\Response;

use radz2k\Dpd\Soap\Types\GenerateProtocolV2Response;

class GenerateProtocolResponse
{
    private $fileContent;

    /**
     * GenerateLabelsResponse constructor.
     *
     * @param $fileContent
     */
    protected function __construct($fileContent)
    {
        $this->fileContent = $fileContent;
    }

    public static function from(GenerateProtocolV2Response $response)
    {
        return new static($response->getReturn()->getDocumentData());
    }

    /**
     * @return string
     */
    public function getFileContent()
    {
        return $this->fileContent;
    }
}
