<?php

namespace radz2k\Dpd\Soap\Client;

use Phpro\SoapClient\Caller\Caller;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Exception\SoapException;
use Soap\Engine\Engine;
use radz2k\Dpd\Soap\Types\GetEventsForCustomerV4Request;
use radz2k\Dpd\Soap\Types\GetEventsForCustomerV4Response;
use radz2k\Dpd\Soap\Types\GetEventsForWaybillV1Request;
use radz2k\Dpd\Soap\Types\GetEventsForWaybillV1Response;
use radz2k\Dpd\Soap\Types\MarkEventsAsProcessedV1Request;
use radz2k\Dpd\Soap\Types\MarkEventsAsProcessedV1Response;
use Debugbar;

class InfoServicesClient implements Caller {
    
    private Engine $engine;

    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    public function __invoke(string $method, RequestInterface $request): ResultInterface
    {
        try {
            $arguments = ($request instanceof MultiArgumentRequestInterface) ? $request->getArguments() : [$request];
            $result = $this->engine->request($method, $arguments);

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

    public function markEventsAsProcessedV1(MarkEventsAsProcessedV1Request $markEventsAsProcessedV1): MarkEventsAsProcessedV1Response {
        return $this->__invoke('markEventsAsProcessedV1', $markEventsAsProcessedV1);
    }

    public function getEventsForWaybillV1(GetEventsForWaybillV1Request $getEventsForWaybillV1): GetEventsForWaybillV1Response {
        return $this->__invoke('getEventsForWaybillV1', $getEventsForWaybillV1);
    }

    public function getEventsForCustomerV4(GetEventsForCustomerV4Request $getEventsForCustomerV4): GetEventsForCustomerV4Response {
        return $this->__invoke('getEventsForCustomerV4', $getEventsForCustomerV4);
    }



}
