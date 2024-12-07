<?php

declare(strict_types=1);

namespace Brightspace\Api\Outcome\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<string> */
final class GetRegistryId extends MessageHandler
{
    public function __construct(private int $orgUnitId)
    {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        $this->useWebLogin(true)->useAccessToken(false);

        return $requestBuilder
            ->setMethod('GET')
            ->setUri(sprintf(
                'd2l://web/d2l/le/lo/%s/outcomes-management',
                $this->orgUnitId,
            ))
            ->getRequest();
    }


    /** @return string */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException();
        }

        $document = new \DOMDocument();
        if (!@$document->loadHTML($response->getBody()->getContents())) {
            throw new \RuntimeException("Unable to parse contents");
        }

        $attributes = $document
            ->getElementsByTagName("d2l-outcomes-management")
            ->item(0)
            ->attributes
            ?? throw new \RuntimeException("Element 'd2l-outcomes-management' not found");

        for ($i = 0; $i < $attributes->length; $i++) {
            $attribute = $attributes->item($i);
            if ($attribute !== null && $attribute->nodeName === 'registry-id' && is_string($attribute->nodeValue)) {
                return $attribute->nodeValue;
            }
        }

        throw new \RuntimeException("Attribute 'registry-id' not found");
    }
}
