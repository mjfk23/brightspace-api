<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth;

use Brightspace\Api\Auth\Factory\LoginTokenFactory;
use Brightspace\Api\Auth\Model\LoginCredentials;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class LoginTokenCache
{
    /**
     * @param CacheItemPoolInterface $cache
     * @param LoginTokenFactory $factory
     * @param LoginCredentials $loginCredentials
     */
    public function __construct(
        private CacheItemPoolInterface $cache,
        private LoginTokenFactory $factory,
        private LoginCredentials $loginCredentials
    ) {
    }


    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->getItem($key)->isHit();
    }


    /**
     * @param string $key
     * @return string|null
     */
    public function get(string $key): string|null
    {
        $item = $this->getItem($key);
        $token = $this->getToken($item);
        return ($token !== null)
            ? $token
            : $this->setToken(
                $item,
                $this->factory->create($this->loginCredentials)
            );
    }


    /**
     * @param string $key
     * @param string|null $token
     * @return string|null
     */
    public function set(
        string $key,
        string|null $token
    ): string|null {
        return $this->setToken(
            $this->getItem($key),
            $token
        );
    }


    /**
     * @param string $key
     * @return string
     */
    private function getKey(string $key): string
    {
        return hash('SHA256', sprintf('%s::%s', self::class, $key));
    }


    /**
     * @param string $key
     * @return CacheItemInterface
     */
    private function getItem(string $key): CacheItemInterface
    {
        return $this->cache->getItem($this->getKey($key));
    }


    /**
     * @param CacheItemInterface $item
     * @return string|null
     */
    private function getToken(CacheItemInterface $item): string|null
    {
        /** @var mixed $token */
        $token = $item->isHit() ? $item->get() : null;
        return is_string($token) ? $token : null;
    }


    /**
     * @param CacheItemInterface $item
     * @param string|null $token
     * @return string|null
     */
    private function setToken(
        CacheItemInterface $item,
        string|null $token
    ): string|null {
        if ($token !== null) {
            $this->cache->save($item->set($token));
        } else {
            $this->cache->deleteItem($item->getKey());
        }
        return $token;
    }
}
