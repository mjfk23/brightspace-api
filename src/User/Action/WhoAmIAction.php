<?php

declare(strict_types=1);

namespace Brightspace\Api\User\Action;

use Brightspace\Api\Core\Action\ApiAction;
use Brightspace\Api\User\Model\WhoAmI;
use Gadget\Http\ApiClient;
use Psr\Http\Message\ResponseInterface;

/** @extends ApiAction<null,WhoAmI> */
class WhoAmIAction extends ApiAction
{
    protected function initAction(...$param): static
    {
        return $this
            ->setMethod('GET')
            ->setUri('d2l://lp/users/whoami/');
    }


    /**
     * @param ResponseInterface $response
     * @return WhoAmI
     */
    protected function parseResponse(ResponseInterface $response): mixed
    {
        return WhoAmI::create(ApiClient::jsonResponse($response));
    }
}
