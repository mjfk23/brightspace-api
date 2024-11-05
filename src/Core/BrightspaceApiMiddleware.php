<?php

declare(strict_types=1);

namespace Brightspace\Api\Core;

use Brightspace\Api\Auth\Middleware\LoginMiddleware;
use Brightspace\Api\Auth\Model\OAuthConfig;
use Brightspace\Api\Core\Model\ProductVersion;
use Gadget\Http\ApiClient;
use Gadget\Io\Cast;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BrightspaceApiMiddleware implements MiddlewareInterface
{
    /**
     * @param OAuthConfig $config
     * @param LoginMiddleware $loginMiddleware
     * @param CacheItemPoolInterface $cache
     * @param ApiClient $apiClient
     */
    public function __construct(
        private OAuthConfig $config,
        private LoginMiddleware $loginMiddleware,
        private CacheItemPoolInterface $cache,
        private ApiClient $apiClient
    ) {
    }


    /** @inheritdoc */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return $this->loginMiddleware->process(
            $this->processUri($request),
            $handler
        );
    }


    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    private function processUri(ServerRequestInterface $request): ServerRequestInterface
    {
        $uri = $request->getUri();
        if ($uri->getScheme() !== 'd2l') {
            return $request;
        }

        $oauthKey = $request->getAttribute($this->config->keyAttrName);
        return $request
            ->withUri(
                $uri
                    ->withPath($this->getPath($uri->getHost(), $uri->getPath()))
                    ->withHost($this->config->hostName)
                    ->withScheme("https")
            )
            ->withAttribute(
                $this->config->keyAttrName,
                match (true) {
                    is_string($oauthKey) => $oauthKey,
                    is_null($oauthKey) => $this->config->defaultKey,
                    default => false
                }
            );
    }


    /**
     * @param string $productCode
     * @param string $path
     * @return string
     */
    private function getPath(
        string $productCode,
        string $path
    ): string {
        return (str_starts_with($path, '/d2l/api/')) || $productCode === 'web'
            ? $path
            : sprintf(
                "/d2l/api/%s/%s%s",
                $productCode,
                $this->getVersion($productCode),
                $path
            );
    }


    /**
     * @param string $productCode
     * @return string
     */
    private function getVersion(string $productCode): string
    {
        $cacheItem = $this->cache->getItem(hash('SHA256', sprintf('%s::%s', static::class, 'getVersions')));

        /** @var array<string,ProductVersion>|null $versions */
        $versions = $cacheItem->isHit() ? $cacheItem->get() : null;
        if ($versions === null) {
            $versions = Cast::toTypedMap(
                $this->apiClient->sendApiRequest(
                    'GET',
                    sprintf(
                        'https://%s/d2l/api/versions/',
                        $this->config->hostName
                    )
                ),
                function (mixed $v): ProductVersion {
                    $v = Cast::toArray($v);
                    return new ProductVersion(
                        ProductCode: Cast::toString($v['ProductCode'] ?? null),
                        LatestVersion: Cast::toString($v['LatestVersion'] ?? null)
                    );
                },
                fn(ProductVersion $pv): string => $pv->ProductCode
            );
            $this->cache->save($cacheItem->set($versions)->expiresAfter(7200));
        }

        return ($versions[$productCode] ?? null)?->LatestVersion ?? "1.0";
    }
}
