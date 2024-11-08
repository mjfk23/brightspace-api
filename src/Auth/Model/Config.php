<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Model;

use Gadget\Http\OAuth\Model\Config as BaseConfig;

final class Config extends BaseConfig
{
    /**
     * @param int $orgId
     * @param string $hostName
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @param string $scope
     * @param string $tokenRequestAttr
     * @param string $tokenCacheKey
     * @param string $loginRequestAttr
     * @param string $loginCacheKey
     */
    public function __construct(
        public int $orgId,
        string $hostName,
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $scope,
        string $tokenRequestAttr = self::class . '::oauthRequestAttr',
        string $tokenCacheKey = self::class . '::oauthCacheKey',
        public string $loginRequestAttr = self::class . '::loginRequestAttr',
        public string $loginCacheKey = self::class . '::loginCacheKey'
    ) {
        parent::__construct(
            hostName: $hostName,
            authCodeUri: 'https://auth.brightspace.com/oauth2/auth',
            tokenUri: 'https://auth.brightspace.com/core/connect/token',
            clientId: $clientId,
            clientSecret: $clientSecret,
            redirectUri: $redirectUri,
            scope: $scope,
            jwksUri: 'https://auth.brightspace.com/core/.well-known/jwks',
            jwksDefaultAlg: 'RS256',
            tokenRequestAttr: $tokenRequestAttr,
            tokenCacheKey: $tokenCacheKey
        );
    }
}
