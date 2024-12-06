<?php

declare(strict_types=1);

namespace Brightspace\Api\User\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\User\Model\User;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<User|null> */
final class GetUser extends MessageHandler
{
    public function __construct(private int $userId)
    {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri("d2l://lp/users/{$this->userId}")
            ->getRequest()
            ;
    }


    /** @return User|null */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return match ($response->getStatusCode()) {
            200 => User::create($this->jsonToArray($response)),
            404 => null,
            default => throw new \RuntimeException()
        };
    }
}
