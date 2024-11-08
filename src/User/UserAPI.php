<?php

declare(strict_types=1);

namespace Brightspace\Api\User;

use Brightspace\Api\User\Action\WhoAmIAction;

class UserAPI
{
    /**
     * @param WhoAmIAction $whoAmI
     */
    public function __construct(public WhoAmIAction $whoAmI)
    {
    }
}
