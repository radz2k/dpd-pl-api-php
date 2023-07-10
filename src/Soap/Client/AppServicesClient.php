<?php

namespace radz2k\Dpd\Soap\Client;

use Phpro\SoapClient\Caller\Caller;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Exception\SoapException;
use Soap\Engine\Engine;
use radz2k\Dpd\Soap\Types\ImportPackagesXV1Request;
use radz2k\Dpd\Soap\Types\ImportPackagesXV1Response;

class AppServicesClient extends Caller
{
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
    
    public function importPackagesXV1(ImportPackagesXV1Request $importPackagesXV1)
    {
        return $this->call('importPackagesXV1', $importPackagesXV1);
    }
}