<?php

declare(strict_types=1);

namespace Brightspace\Api\Core\Middleware;

use Brightspace\Api\Auth\Cache\ProductVersionCache;
use Brightspace\Api\Auth\Model\AuthConfig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class UriMiddleware implements MiddlewareInterface
{
    /**
     * @param AuthConfig $authConfig
     * @param ProductVersionCache $productVersionCache
     */
    public function __construct(
        private AuthConfig $authConfig,
        private ProductVersionCache $productVersionCache
    ) {
    }


    /** @inheritdoc */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $uri = $request->getUri();
        return $handler->handle(
            ($uri->getScheme() === 'd2l')
                ? $request
                    ->withUri(
                        $uri
                            ->withPath($this->getPath($uri->getHost(), $uri->getPath()))
                            ->withHost($this->authConfig->hostName)
                            ->withScheme("https")
                    )
                : $request
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
        return (str_starts_with($path, '/d2l/api/')) || in_array($productCode, ['api', 'web'], true)
            ? $path
            : sprintf(
                "/d2l/api/%s/%s%s",
                $productCode,
                $this->productVersionCache->getVersion($productCode)?->LatestVersion ?? "1.0",
                $path
            );
    }
}
