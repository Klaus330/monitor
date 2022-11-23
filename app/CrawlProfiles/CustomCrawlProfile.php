<?php

namespace App\CrawlProfiles;

use App\Models\Site;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlProfiles\CrawlProfile;

class CustomCrawlProfile extends CrawlProfile {

    protected UriInterface $baseUrl;

    public function __construct(Site $site)
    {
        $this->baseUrl = $site->uri;
    }

    public function shouldCrawl(UriInterface $url): bool
    {
        // TODO: Check if subdomain check is enabled
        if($this->isSubdomainOfHost($url))
        {
            return true;
        }

        if($this->baseUrl->getHost() === $url->getHost())
        {
            return true;
        }

        return false;
    }

    public function isSubdomainOfHost(UriInterface $url): bool
    {
        return str_ends_with($url->getHost(), $this->baseUrl->getHost());
    }
}