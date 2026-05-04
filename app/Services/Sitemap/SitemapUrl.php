<?php

declare(strict_types=1);

namespace App\Services\Sitemap;

final readonly class SitemapUrl
{
    public function __construct(
        public string $loc,
        public ?string $lastmod = null,
        public ?string $changefreq = null,
        public ?float $priority = null,
    ) {
    }
}
