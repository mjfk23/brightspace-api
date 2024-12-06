<?php

declare(strict_types=1);

namespace Brightspace\Api\User\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\Core\Model\PagedResultSet;
use Brightspace\Api\User\Model\User;
use Gadget\Http\Message\RequestBuilder;
use Gadget\Io\Cast;
use Gadget\Io\JSON;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<iterable<int,User>> */
final class ListUsers extends MessageHandler
{
    public function __construct(
        private string|null $orgDefinedId = null,
        private string|null $userName = null,
        private string|null $externalEmail = null,
        private string|null $bookmark = null
    ) {
        if ($this->orgDefinedId !== null) {
            $this->userName = null;
            $this->externalEmail = null;
            $this->bookmark = null;
        } elseif ($this->userName !== null) {
            $this->externalEmail = null;
            $this->bookmark = null;
        } elseif ($this->externalEmail !== null) {
            $this->bookmark = null;
        }
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri("d2l://lp/users/")
            ->setQueryParams([
                'orgDefinedId' => $this->orgDefinedId,
                'userName' => $this->userName,
                'externalEmail' => $this->externalEmail,
                'bookmark' => $this->bookmark
            ])
            ->getRequest()
            ;
    }


    /** @return iterable<int,User> */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException();
        }

        if ($this->userName !== null) {
            $result = JSON::decode($response->getBody()->getContents());

            $bookmark = null;
            $users = ($result !== null) ? [User::create($result)] : [];
        } elseif ($this->orgDefinedId !== null || $this->externalEmail !== null) {
            $result = $this->jsonToArray($response);

            $bookmark = null;
            $users = Cast::toTypedArray(
                $result,
                User::create(...)
            );
        } else {
            $result = PagedResultSet::create(
                $this->jsonToArray($response),
                User::create(...)
            );

            $bookmark = $result->pagingInfo->bookmark;
            $users = $result->items;
        }

        foreach ($users as $user) {
            yield $user->UserId => $user;
        }

        $this->bookmark = $bookmark;
        if ($this->bookmark !== null) {
            yield from $this->invoke($this->getClient());
        }
    }
}
