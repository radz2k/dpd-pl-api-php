<?php

namespace radz2k\Dpd\Soap\Client;

use Phpro\SoapClient\Caller\Caller;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Exception\SoapException;
use Soap\Engine\Engine;
//use Debugbar;

class AppServicesClient implements Caller
{
    private Engine $engine;

    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    public function __invoke(string $method, RequestInterface $request): ResultInterface
    {
        
        try {
            
//            // Debugbar (request)
//            Debugbar::addMessage($request);
            
            $arguments = ($request instanceof MultiArgumentRequestInterface) ? $request->getArguments() : [$request];
            $result = $this->engine->request($method, $arguments);
            
//            // Debugbar (response)
//            $xml = simplexml_load_string($result->getReturn());
//            $array = json_decode(json_encode($xml), true);
//            Debugbar::addMessage($array, 'array');

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
    
    public function importPackagesXV1(\radz2k\Dpd\Soap\Types\ImportPackagesXV1Request $importPackagesXV1) : \radz2k\Dpd\Soap\Types\ImportPackagesXV1Response
    {
        return $this->__invoke('importPackagesXV1', $importPackagesXV1);
    }
}