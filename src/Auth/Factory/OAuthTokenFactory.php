<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Factory;

use Brightspace\Api\Auth\Model\OAuthConfig;
use Gadget\Http\ApiClient;
use Gadget\Http\OAuth\OAuthToken;
use Gadget\Http\OAuth\OAuthTokenFactory as BaseOAuthTokenFactory;
use Psr\Cache\CacheItemPoolInterface;

final class OAuthTokenFactory extends BaseOAuthTokenFactory
{
    /** @inheritdoc */
    public function __construct(
        private OAuthConfig $brightspaceConfig,
        ApiClient $apiClient,
        CacheItemPoolInterface $cache
    ) {
        parent::__construct($brightspaceConfig, $apiClient, $cache);
    }


    /**
     * @param string $loginToken
     * @return OAuthToken
     */
    public function fromLoginToken(string $loginToken): OAuthToken
    {
        list($authCodeUri) = $this->getAuthCodeUri();
        list($state, $authCode) = $this->fetchAuthCode(
            $loginToken,
            $authCodeUri,
            $this->config->redirectUri
        );

        return $this->fromAuthCode(
            $state,
            $authCode
        );
    }


    /**
     * @param string $loginToken
     * @param string $authCodeUri
     * @param string $redirectUri
     * @return array{string,string}
     */
    private function fetchAuthCode(
        string $loginToken,
        string $authCodeUri,
        string $redirectUri
    ): array {
        $url = $authCodeUri;

        do {
            $request = $this->apiClient->createRequest('GET', $url);
            if (str_starts_with($url, "https://{$this->brightspaceConfig->hostName}")) {
                $request = $request->withHeader('Cookie', $loginToken);
            }
            $response = $this->apiClient->sendRequest($request);
            $url = $response->getStatusCode() === 302
                ? ($response->getHeader('Location')[0] ?? null)
                : null;
        } while ($url !== null && !str_starts_with($url, $redirectUri));

        $authCode = match (true) {
            $url === null => throw new \RuntimeException("Error creating authorization code"),
            str_starts_with($url, $redirectUri . '/?') => substr($url, strlen($redirectUri . '/?')),
            default => substr($url, strlen($redirectUri . '?'))
        };

        /** @var array<string,string> $params */
        $params = array_map(
            fn(string $v): string => urldecode($v),
            array_column(array_map(fn($v) => explode('=', $v), explode("&", $authCode)), 1, 0)
        );

        return [
            $params['state'] ?? '',
            $params['code'] ?? ''
        ];
    }
}
