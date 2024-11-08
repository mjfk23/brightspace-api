<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Cache;

use Brightspace\Api\Auth\Factory\LoginTokenFactory;
use Brightspace\Api\Auth\Model\LoginCredentials;
use Gadget\Cache\CacheItemPool;
use Psr\Cache\CacheItemInterface;

final class LoginTokenCache
{
    /**
     * @param CacheItemPool $cache
     * @param LoginTokenFactory $factory
     * @param LoginCredentials $credentials
     */
    public function __construct(
        private CacheItemPool $cache,
        private LoginTokenFactory $factory,
        private LoginCredentials $credentials
    ) {
        $this->cache = $cache->withNamespace(self::class);
    }


    /**
     * @param string $key
     * @param LoginCredentials|null $credentials
     * @return string
     */
    public function get(
        string $key,
        LoginCredentials|null $credentials = null
    ): string {
        $item = $this->cache->get($key);
        $token = $item->isHit() ? $item->get() : null;
        return is_string($token)
            ? $token
            : $this->set(
                $key,
                $this->factory->create($credentials ?? $this->credentials)
            );
    }


    /**
     * @param string $key
     * @param string $token
     * @return string
     */
    public function set(
        string $key,
        string $token
    ): string {
        $this->cache->save(
            $this->cache
                ->get($key)
                ->set($token)
                ->expiresAfter(900)
        );
        return $token;
    }
}
