<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Factory;

use Brightspace\Api\Auth\Model\Config;
use Gadget\Http\ApiClient;
use Gadget\Http\OAuth\Cache\AuthCodeCache;
use Gadget\Http\OAuth\Factory\AuthCodeFactory as BaseAuthCodeFactory;
use Gadget\Http\OAuth\Model\AuthCode;
use Gadget\Http\OAuth\Model\PKCE;

final class AuthCodeFactory extends BaseAuthCodeFactory
{
    /**
     * @param Config $config
     * @param AuthCodeCache $cache
     * @param ApiClient $apiClient
     */
    public function __construct(
        Config $config,
        AuthCodeCache $cache,
        private ApiClient $apiClient
    ) {
        parent::__construct($config, $cache);
    }


    /**
     * @param string $loginToken
     * @param string|null $state
     * @param PKCE|null $pkce
     * @return AuthCode
     */
    public function createFromLoginToken(
        string $loginToken,
        string|null $state = null,
        PKCE|null $pkce = null
    ): AuthCode {
        $authCode = $this->create($state, $pkce);

        $url = $authCode->uri;

        do {
            $request = $this->apiClient->createRequest('GET', $url);
            if (str_starts_with($url, "https://{$this->config->hostName}")) {
                $request = $request->withHeader('Cookie', $loginToken);
            }
            $response = $this->apiClient->sendRequest($request);
            $url = $response->getStatusCode() === 302
                ? ($response->getHeader('Location')[0] ?? null)
                : null;
        } while ($url !== null && !str_starts_with($url, $this->config->redirectUri));

        $code = match (true) {
            $url === null => throw new \RuntimeException("Error creating authorization code"),
            str_starts_with($url, $this->config->redirectUri . '/?') => substr(
                $url,
                strlen($this->config->redirectUri . '/?')
            ),
            default => substr($url, strlen($this->config->redirectUri . '?'))
        };

        /** @var array<string,string> $params */
        $params = array_map(
            fn(string $v): string => urldecode($v),
            array_column(array_map(fn($v) => explode('=', $v), explode("&", $code)), 1, 0)
        );

        if ($authCode->state !== ($params['state'] ?? null)) {
            throw new \RuntimeException();
        }
        $this->cache->delete($authCode->state);
        $authCode->code = $params['code'] ?? null;
        return $authCode;
    }
}
