<?php

declare(strict_types=1);

namespace Brightspace\Api\User\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\User\Model\User;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<User> */
final class UpdateUser extends MessageHandler
{
    public function __construct(private User $updateUser)
    {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('PUT')
            ->setUri("d2l://lp/users/{$this->updateUser->UserId}")
            ->setBody(RequestBuilder::JSON, $this->updateUser->getUpdatePayload())
            ->getRequest()
            ;
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
