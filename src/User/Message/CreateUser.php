<?php

declare(strict_types=1);

namespace Brightspace\Api\User\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\User\Model\User;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<User> */
final class CreateUser extends MessageHandler
{
    public function __construct(private User $newUser)
    {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('POST')
            ->setUri('d2l://lp/users/')
            ->setBody(RequestBuilder::JSON, $this->newUser->getCreatePayload())
            ->getRequest();
    }


    /** @return User */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return $response->getStatusCode() === 200
            ? User::create($this->jsonToArray($response))
            : throw new \RuntimeException();
    }
}
