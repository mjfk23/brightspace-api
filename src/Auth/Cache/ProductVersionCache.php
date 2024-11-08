<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Cache;

use Brightspace\Api\Core\Model\ProductVersion;
use Gadget\Cache\CacheItemPool;
use Gadget\Http\ApiClient;
use Gadget\Io\Cast;

final class ProductVersionCache
{
    /**
     * @param CacheItemPool $cache
     * @param ApiClient $apiClient
     */
    public function __construct(
        private CacheItemPool $cache,
        private ApiClient $apiClient
    ) {
        $this->cache = $cache->withNamespace(self::class);
    }


    /**
     * @param string $productCode
     * @return ProductVersion|null
     */
    public function getVersion(string $productCode): ProductVersion|null
    {
        return $this->getVersions()[$productCode] ?? null;
    }


    /**
     * @return array<string,ProductVersion>
     */
    public function getVersions(): array
    {
        $cacheItem = $this->cache->get('productVersions');
        /** @var array<string,ProductVersion>|null $versions */
        $versions = $cacheItem->isHit() ? $cacheItem->get() : null;
        if ($versions === null) {
            $versions = $this->fetchVersions();
            $this->cache->save($cacheItem->set($versions)->expiresAfter(7200));
        }
        return $versions;
    }


    /**
     * @return array<string,ProductVersion>
     */
    private function fetchVersions(): array
    {
        return Cast::toTypedMap(
            $this->apiClient->sendApiRequest(
                method: 'GET',
                uri: 'd2l://api/d2l/api/versions/',
                parseResponse: ApiClient::jsonResponse(...)
            ),
            $this->createVersion(...),
            fn(ProductVersion $pv): string => $pv->ProductCode
        );
    }


    /**
     * @param mixed $v
     * @return ProductVersion
     */
    private function createVersion(mixed $v): ProductVersion
    {
        $v = Cast::toArray($v);
        return new ProductVersion(
            ProductCode: Cast::toString($v['ProductCode'] ?? null),
            LatestVersion: Cast::toString($v['LatestVersion'] ?? null)
        );
    }
}
