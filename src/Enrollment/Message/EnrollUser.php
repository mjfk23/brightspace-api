<?php

declare(strict_types=1);

namespace Brightspace\Api\Enrollment\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<bool> */
final class EnrollUser extends MessageHandler
{
    public function __construct(
        private int $orgUnitId,
        private int $userId,
        private int $roleId
    ) {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('POST')
            ->setUri("d2l://lp/enrollments/")
            ->setBody(RequestBuilder::JSON, [
                'orgUnitId' => $this->orgUnitId,
                'userId' => $this->userId,
                'roleId' => $this->roleId
            ])
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
