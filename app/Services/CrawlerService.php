<?php

namespace App\Services;

use App\Jobs\CrawlSite;
use App\Models\Site;
use App\Repositories\SiteRouteRepository;
use Carbon\Carbon;
use GuzzleHttp\TransferStats;
use Illuminate\Database\QueryException;
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

        $responseTime = 0;
        sleep($site->configuration->getCrawlerDelayInMiliseconds());

        try {
            $url = $site->getUrl() . '/' . $route;

            $response = Http::get($url, [
                "verify" => true,
                'headers' => [
                    'User-Agent' => "Oopsie.app"
                ]
            ]);

            dd($response->transferStats);
            $this->registerRouteAsCrawled([
                'site_id' => $site->id,
                'route' => $route,
                'found_on' => $foundOnRoute,
                'http_code' => $response->status(),
                'response_time' => round($response->transferStats->getTransferTime() / 1000)
            ]);

            $routes = $this->fetchAllRelatedLinks($response->body(), $site);
            $this->crawlFoundRoutes($site, $routes, $route);
        } catch (ConnectionException $e) {
            $this->registerRouteAsCrawled([
                'site_id' => $site->id,
                'route' => $route,
                'response_time' => 0,
                'found_on' => $foundOnRoute,
                'http_code' => self::BAD_REQUEST_CODE
            ]);

            Log::error($e);
        }
    }

    protected function fetchAllRelatedLinks(string $content, Site $site): array
    {
        preg_match_all('/<a.*?href="(.*?)".*?>/', $content, $matches);


        $routes = array_map(function ($el) use ($site) {
            $trimmedRoute = trim($el, '/');

            if (!$this->isOriginatedFromSite($el, $site) || $this->routeAlreadyVisited($site, $trimmedRoute)) {
                return "";
            }

            $trimmedRoute = trim(preg_replace("/https?:\/\/{$site->domainName}/", '', $trimmedRoute), '/');
            return $trimmedRoute;
        }, $matches[1]);

        return array_unique(array_filter($routes));
    }

    protected function isOriginatedFromSite(string $url, Site $site): bool
    {
        $isValid = strpos($url, $site->url) === 0 || substr($url, 0, 1) === '/' || substr($url, 0, 1) === '?';

        if (!$site->configuration->respect_robots) {
            $isValid = $isValid && !$site->forbiddenRoute($url);
        }

        return $isValid;
    }

    protected function registerRouteAsCrawled(array $data): void
    {
        if ($this->routeRepository->routeHasReachedSnapshotLimit($data['site_id'], $data['route'])) {

            $route = $this->routeRepository->findLastModifiedSnapshot($data['site_id'], $data['route']);

            $route->update([
                'http_code' => $data['http_code'],
                'found_on' => $data['found_on'],
            ]);

            $route->touch();

            return;
        }

        $this->routeRepository->create($data);
    }

    protected function crawlFoundRoutes(Site $site, array $routes, string $foundOnRoute): void
    {
        foreach ($routes as $route) {
            if ($this->routeAlreadyVisited($site, $route)) {
                continue;
            }

            dispatch(new CrawlSite($site, $this, $route, $foundOnRoute))->onQueue('crawlers');
        }
    }

    protected function routeAlreadyVisited(Site $site, string $route): bool
    {
        return $this->routeRepository->routeForSiteExists($site->id, trim($route, " "));
    }
}
