<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Model;

use Gadget\Http\OAuth\OAuthConfig;

final class AuthConfig extends OAuthConfig
{
    /**
     * @param string $hostName
     * @param int $orgId
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @param string $scope
     * @param string $keyAttrName
     * @param string $defaultKey
     * @param string $loginKeyAttrName
     * @param string $defaultLoginKey
     */
    public function __construct(
        public string $hostName,
        public int $orgId,
        public string $clientId,
        public string $clientSecret,
        public string $redirectUri,
        public string $scope,
        public string $keyAttrName = 'oauthTokenKey',
        public string $defaultKey = 'oauthDefault',
        public string $loginKeyAttrName = 'loginTokenKey',
        public string $defaultLoginKey = 'loginDefault'
    ) {
        parent::__construct(
            authCodeUri: 'https://auth.brightspace.com/oauth2/auth',
            tokenUri: 'https://auth.brightspace.com/core/connect/token',
            clientId: $clientId,
            clientSecret: $clientSecret,
            redirectUri: $redirectUri,
            scope: $scope,
            jwksUri: 'https://auth.brightspace.com/core/.well-known/jwks',
            jwksDefaultAlg: 'RS256',
            keyAttrName: $keyAttrName,
            defaultKey: $defaultKey
        );
    }
}
