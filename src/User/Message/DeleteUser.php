<?php

declare(strict_types=1);

namespace Brightspace\Api\User\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<bool> */
final class DeleteUser extends MessageHandler
{
    public function __construct(private int $userId)
    {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('DELETE')
            ->setUri("d2l://lp/users/{$this->userId}")
            ->getRequest()
            ;
    }


    /** @return bool */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return $response->getStatusCode() === 200
            ? true
            : throw new \RuntimeException();
    }
}
