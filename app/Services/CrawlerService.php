<?php

namespace App\Services;

use App\Jobs\CrawlSite;
use App\Models\Site;
use App\Repositories\SiteRouteRepository;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrawlerService
{
    protected const BAD_REQUEST_CODE = 400;

    public function __construct(protected SiteRouteRepository $routeRepository)
    {
    }

    public function crawl(Site $site, string $route, string $foundOnRoute): void
    {
        if ($this->routeAlreadyVisited($site, $route)) {
            return;
        }

        try {
            $url = $site->getUrl() . '/' . $route;

            $response = Http::withOptions(["verify" => false])->get($url);
            $this->registerRouteAsCrawled([
                'site_id' => $site->id,
                'route' => $route,
                'found_on' => $foundOnRoute,
                'http_code' => $response->status()
            ]);

            $routes = $this->fetchAllRelatedLinks($response->body(), $site);

            $this->crawlFoundRoutes($site, $routes, $route);
        } catch (ConnectionException $e) {
            $this->registerRouteAsCrawled([
                'site_id' => $site->id,
                'route' => $route,
                'found_on' => $foundOnRoute,
                'http_code' => self::BAD_REQUEST_CODE
            ]);

            Log::error($e);
        }
    }

    protected function fetchAllRelatedLinks($content, $site)
    {
        preg_match_all('/<a.*?href="(.*?)".*?>/', $content, $matches);

        $routes = array_map(function ($el) use ($site) {
            $trimmedRoute = trim($el, '/');

            if (!$this->isOriginatedFromSite($el, $site) || $this->routeAlreadyVisited($site, $trimmedRoute)) {
                return "";
            }

            return $trimmedRoute;
        }, $matches[1]);

        return array_unique(array_filter($routes));
    }

    protected function isOriginatedFromSite($url, $site)
    {
        return strpos($url, $site->url) === 0 || substr($url, 0, 1) === '/' || substr($url, 0, 1) === '?';
    }

    protected function registerRouteAsCrawled(array $data): void
    {
        $this->routeRepository->create($data);
    }

    protected function crawlFoundRoutes($site, $routes, $foundOnRoute)
    {
        foreach ($routes as $route) {
            if ($this->routeAlreadyVisited($site, $route)) {
                continue;
            }

            dispatch(new CrawlSite($site, $this, $route, $foundOnRoute))->onQueue('crawlers');
        }
    }

    protected function routeAlreadyVisited($site, $route)
    {
        return $this->routeRepository->routeForSiteExists($site->id, trim($route, " "));
    }
}
