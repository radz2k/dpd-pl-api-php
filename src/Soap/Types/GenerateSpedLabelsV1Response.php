<?php

namespace radz2k\Dpd\Soap\Types;

use Phpro\SoapClient\Type\ResultInterface;

class GenerateSpedLabelsV1Response implements ResultInterface
{

    /**
     * @var DocumentGenerationResponseV1
     */
    private $return;

    /**
     * @return DocumentGenerationResponseV1
     */
    public function getReturn() : DocumentGenerationResponseV1
    {
        return $this->return;
    }

    /**
     * @param DocumentGenerationResponseV1 $return
     * @return $this
     */
    public function setReturn(DocumentGenerationResponseV1 $return) : GenerateSpedLabelsV1Response
    {
        $this->return = $return;
        return $this;
    }


}
