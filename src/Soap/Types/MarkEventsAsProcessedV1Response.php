<?php

namespace radz2k\Dpd\Soap\Types;

use Phpro\SoapClient\Type\ResultInterface;

class MarkEventsAsProcessedV1Response implements ResultInterface
{
    /**
     * @var bool
     */
    private $return;

    /**
     * @return bool
     */
    public function isReturn() : bool
    {
        return $this->return;
    }
}

