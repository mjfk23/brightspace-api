<?php

declare(strict_types=1);

namespace Brightspace\Api\Core\Model;

final class ProductVersion
{
    /**
     * @param string $ProductCode
     * @param string $LatestVersion
     */
    public function __construct(
        public string $ProductCode,
        public string $LatestVersion
    ) {
    }
}
