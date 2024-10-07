<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Model;

class LoginCredentials
{
    /**
     * @param string $user
     * @param string $pass
     * @param int|string|null $mfa
     */
    public function __construct(
        public string $user,
        public string $pass,
        public int|string|null $mfa = null,
    ) {
        if ($mfa === 0 || $mfa === '') {
            $this->mfa = $mfa = null;
        }
    }
}
