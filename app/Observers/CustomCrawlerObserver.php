<?php

namespace App\Observers;

use App\Events\BrokenLinksFixed;
use App\Events\SiteCrawled;
use App\Models\Site;
use App\Repositories\SiteRouteRepository;
use DOMDocument;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class CustomCrawlerObserver extends CrawlObserver
{
    protected const BAD_REQUEST_CODE = 400;

    protected array $crawledLinks = [];
    protected array $brokenRoutes = [];

    public function __construct(protected Site $site, protected SiteRouteRepository $siteRouteRepository)
    {
        $this->brokenRoutes = $siteRouteRepository->findBrokenRoutesForSite($site->id)
                                                ->pluck('route')
                                                ->transform(fn ($item) =>  trim($item, '/'))
                                                ->toArray();
    }
    /**
     * Called when the crawler will crawl the url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     */
    public function willCrawl(UriInterface $url): void
    {
        Log::info('willCrawl', ['url' => $url]);
    }

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    ): void {
        $this->registerRoute($url, $response, $foundOnUrl);
    }

    protected function registerRoute(
        UriInterface $url,
        ?ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    ): void {
        $urlPath = trim($url->getPath(), '/');
        $foundOnUrlPath = trim($foundOnUrl?->getPath(), '/');

        if ($this->siteRouteRepository->routeHasReachedSnapshotLimit($this->site->id, $urlPath)) {
            $route = $this->siteRouteRepository->findLastModifiedSnapshot($this->site->id, $urlPath);

            $route->update([
                'http_code' => $response?->getStatusCode() ?? self::BAD_REQUEST_CODE,
                'found_on' => $foundOnUrlPath,
                'updated_at' => now()
            ]);

            $this->crawledLinks[] = $urlPath;

            return;
        }

        $this->siteRouteRepository->create([
            'site_id' => $this->site->id,
            'route' => $urlPath,
            'found_on' => $foundOnUrlPath,
            'http_code' => $response?->getStatusCode() ?? self::BAD_REQUEST_CODE,
        ]);

        $this->crawledLinks[] = $urlPath;
    }

    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \GuzzleHttp\Exception\RequestException $requestException
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ): void {
        Log::error('crawlFailed', ['url' => $url, 'error' => $requestException->getMessage()]);

        $this->registerRoute($url, $requestException->getResponse(), $foundOnUrl);
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
        Log::info("finishedCrawling");

        $theRestOfTheRoutes = array_diff($this->brokenRoutes, $this->crawledLinks);

        if (count($theRestOfTheRoutes) < count($this->brokenRoutes)) {
            BrokenLinksFixed::dispatch($this->site, $this->brokenRoutes);
        }

        SiteCrawled::dispatch($this->site);
    }
}
