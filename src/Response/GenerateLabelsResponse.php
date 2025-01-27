<?php

namespace radz2k\Dpd\Response;

use radz2k\Dpd\Soap\Types\GenerateSpedLabelsV1Response;

class GenerateLabelsResponse
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

    public static function from(GenerateSpedLabelsV1Response $response)
    {
        return new static($response->getReturn()->getDocumentData());
    }

    /**
     * @return mixed
     */
    public function getFileContent()
    {
        return $this->fileContent;
    }
}
