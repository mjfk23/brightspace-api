<?php

declare(strict_types=1);

namespace Brightspace\Api\Outcome\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\Outcome\Model\OutcomeRegistry;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<bool> */
final class UpdateRegistry extends MessageHandler
{
    public function __construct(private OutcomeRegistry $registry)
    {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('PUT')
            ->setUri(sprintf(
                'https://lores-us-east-1.brightspace.com/api/lores/1.0/registries/%s',
                $this->registry->id,
            ))
            ->setHeader('Host', 'lores-us-east-1.brightspace.com')
            ->setBody(RequestBuilder::JSON, ['objectives' => $this->registry->objectives])
            ->getRequest();
    }


    /** @return bool */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return $response->getStatusCode() === 200;
    }
}
