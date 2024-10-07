<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Model;

final class LoginContext extends LoginCredentials
{
    /**
     * @param LoginCredentials|null $credentials
     * @param string $loginToken
     * @param string[] $location
     * @param string $xsrfName
     * @param string $xsrfCode
     * @param string $hitCodeSeed
     * @param int $hitCode
     * @param string $mfaCode
     */
    public function __construct(
        LoginCredentials $credentials = null,
        public string $loginToken = '',
        public array $location = [],
        public string $xsrfName = '',
        public string $xsrfCode = '',
        public string $hitCodeSeed = '0',
        public int $hitCode = 0,
        public string $mfaCode = ''
    ) {
        parent::__construct(
            $credentials?->user ?? '',
            $credentials?->pass ?? '',
            $credentials?->mfa ?? null
        );
    }
}
