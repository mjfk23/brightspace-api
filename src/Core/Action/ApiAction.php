<?php

declare(strict_types=1);

namespace Brightspace\Api\Core\Action;

use Gadget\Http\ApiClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * @template TRequest
 * @template TResponse
 */
abstract class ApiAction
{
    /**
     * @param ApiClient $apiClient
     * @param string $method
     * @param UriInterface|string $uri
     * @param array<string,string|string[]> $headers
     * @param TRequest|null $body
     * @param bool $skipStatusCodeCheck
     */
    public function __construct(
        private ApiClient $apiClient,
        private string $method = '',
        private UriInterface|string $uri = '',
        private array $headers = [],
        private mixed $body = null,
        private bool $skipStatusCodeCheck = false
    ) {
    }


    /**
     * @param mixed $param
     * @return TResponse
     */
    public function invoke(...$param): mixed
    {
        return $this
            ->initAction($param)
            ->sendApiRequest();
    }


    /**
     * @param mixed ...$param
     * @return static
     */
    abstract protected function initAction(...$param): static;


    /**
     * @return ApiClient
     */
    protected function getApiClient(): ApiClient
    {
        return $this->apiClient;
    }


    /**
     * @return string
     */
    protected function getMethod(): string
    {
        return $this->method;
    }


    /**
     * @param string $method
     * @return static
     */
    protected function setMethod(string $method): static
    {
        $this->method = $method;
        return $this;
    }


    /**
     * @return UriInterface
     */
    protected function getUri(): UriInterface|string
    {
        return $this->uri;
    }


    /**
     * @param UriInterface|string $uri
     * @return static
     */
    protected function setUri(UriInterface|string $uri): static
    {
        $this->uri = $uri;
        return $this;
    }


    /**
     * @return array<string,string|string[]>
     */
    protected function getHeaders(): array
    {
        return $this->headers;
    }


    /**
     * @param array<string,string|string[]> $headers
     * @return static
     */
    protected function setHeaders(array $headers): static
    {
        $this->headers = $headers;
        return $this;
    }


    /**
     * @return TRequest|null
     */
    protected function getBody(): mixed
    {
        return $this->body;
    }


    /**
     * @param TRequest|null $body
     * @return static
     */
    protected function setBody(mixed $body): static
    {
        $this->body = $body;
        return $this;
    }


    /**
     * @return bool
     */
    protected function getSkipStatusCodeCheck(): bool
    {
        return $this->skipStatusCodeCheck;
    }


    /**
     * @param bool $skipStatusCodeCheck
     * @return static
     */
    protected function setSkipStatusCodeCheck(bool $skipStatusCodeCheck): static
    {
        $this->skipStatusCodeCheck = $skipStatusCodeCheck;
        return $this;
    }


    /**
     * @param ResponseInterface $response
     * @return TResponse
     */
    abstract protected function parseResponse(ResponseInterface $response): mixed;


    /**
     * @return TResponse
     */
    protected function sendApiRequest(): mixed
    {
        return $this->getApiClient()->sendApiRequest(
            $this->getMethod(),
            $this->getUri(),
            $this->getHeaders(),
            $this->getBody(),
            $this->parseResponse(...),
            $this->getSkipStatusCodeCheck()
        );
    }
}
