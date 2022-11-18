<?php

namespace App\Services;

use App\Jobs\CrawlSite;
use App\Models\Site;
use App\Repositories\SiteRouteRepository;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\TransferStats;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\UriInterface;

class CrawlerService
{
    protected const BAD_REQUEST_CODE = 400;

    protected string $userAgent = "Oopsie.app";

    protected bool $verifySslCertificate = true;

    protected UriInterface $siteUri;

    public function __construct(protected SiteRouteRepository $routeRepository)
    {
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function userAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getVerifySslCertificate(): string
    {
        return $this->verifySslCertificate;
    }

    public function verifySslCertificate(bool $verify = true): self
    {
        $this->verifySslCertificate = $verify;

        return $this;
    }

    public function crawl(Site $site, string $route, string $foundOnRoute)
    {
        if ($this->routeAlreadyVisited($site, $route)) {
            return;
        }

        $this->siteUri = new Uri($site->url);
        $url = $this->siteUri->withPath($route);

        try {
            sleep($site->configuration->getCrawlerDelayInMiliseconds());

            $response = Http::get($url, $this->getCrawlerConfig());

            $this->registerRouteAsCrawled([
                'site_id' => $site->id,
                'route' => $url->getPath(),
                'found_on' => $foundOnRoute,
                'http_code' => $response->status(),
                'response_time' => round($response->transferStats->getTransferTime() * 1000)
            ]);

            $routes = $this->fetchAllRelatedLinks($response->body(), $site);

            $this->dispatchFoundRoutes($site, $routes, $route);
            
        } catch (ConnectionException $e) {
            $this->registerRouteAsCrawled([
                'site_id' => $site->id,
                'route' => $url->getPath(),
                'response_time' => 0,
                'found_on' => $foundOnRoute,
                'http_code' => self::BAD_REQUEST_CODE
            ]);

            Log::error($e);
        }
    }

    private function getCrawlerConfig()
    {
        return [
            "verify" => $this->verifySslCertificate,
            'headers' => [
                'User-Agent' => $this->userAgent
            ]
        ];
    }

    protected function fetchAllRelatedLinks(string $content, Site $site): Collection
    {
        preg_match_all('/<a.*?href="(.*?)".*?>/', $content, $matches);

        $routesUri = collect($matches[1])->map(function ($item) {
            return new Uri($item);
        })->filter(function ($uri) {
            return $this->isValidUri($uri) && $this->respectsRobots($uri);
        })->uniqueStrict(function ($item) {
            return $item->getPath();
        });

        return $routesUri;
    }

    public function isValidUri($uri)
    {
        if ($uri->getFragment() !== '' || $uri->getQuery() !== '') {
            return false;
        }

        if (Uri::isSameDocumentReference($uri, $this->siteUri)) {
            return true;
        }

        if (Uri::isAbsolutePathReference($uri) && $uri->getHost() === '') {
            return true;
        }

        if (Uri::isRelativePathReference($uri) && $uri->getHost() === '') {
            return true;
        }

        if ($uri->getHost() !== $this->siteUri->getHost()) {
            return false;
        }

        return true;
    }

    protected function respectsRobots(): bool
    {
        // if (!$site->configuration->respect_robots) {
        //     $isValid = $isValid && !$site->forbiddenRoute($url);
        // }

        // return $true;

        //  TODO: IMPLEMENTS REPECTS ROBOTS

        return true;
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

    protected function dispatchFoundRoutes(Site $site, Collection $routes, string $foundOnRoute): void
    {
        $routes->each(function (UriInterface $route) use ($site, $foundOnRoute) {
            if (!$this->routeAlreadyVisited($site, $route)) {
                dispatch(new CrawlSite($site, $this, $route, $foundOnRoute))->onQueue('crawlers');
            }
        });
    }

    protected function routeAlreadyVisited(Site $site, string $route): bool
    {
        return $this->routeRepository->routeForSiteExists($site->id, trim($route, " "));
    }
}
