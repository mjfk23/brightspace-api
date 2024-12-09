<?php

declare(strict_types=1);

namespace Brightspace\Api\Outcome\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\Outcome\Model\OutcomeRegistry;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<OutcomeRegistry|null> */
final class GetRegistry extends MessageHandler
{
    public function __construct(private string $registryId)
    {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri(sprintf(
                'https://lores-us-east-1.brightspace.com/api/lores/1.0/registries/%s',
                $this->registryId,
            ))
            ->getRequest();
    }


    /** @return OutcomeRegistry|null */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return match ($response->getStatusCode()) {
            200 => OutcomeRegistry::create($this->jsonToArray($response)),
            404 => null,
            default => throw new \RuntimeException()
        };
    }
}
