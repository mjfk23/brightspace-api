<?php

declare(strict_types=1);

namespace Brightspace\Api\Core;

use Gadget\Log\LoggerProxyInterface;
use Gadget\Log\LoggerProxyTrait;
use Psr\Cache\CacheItemPoolInterface;

abstract class BrightspaceApiController implements LoggerProxyInterface
{
    use LoggerProxyTrait;


    public function __construct(
        protected CacheItemPoolInterface $cache,
        protected BrightspaceApiClient $apiClient
    ) {
    }


    protected function getCacheKey(string $key): string
    {
        return hash('SHA256', sprintf('%s::%s', static::class, $key));
    }
}
