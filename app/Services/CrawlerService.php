<?php

namespace App\Services;

use App\CrawlProfiles\CustomCrawlProfile;
use App\Jobs\CrawlSite;
use App\Models\Site;
use App\Observers\CustomCrawlerObserver;
use App\Repositories\SiteConfigurationRepository;
use App\Repositories\SiteRouteRepository;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\UriInterface;
use Spatie\Browsershot\Browsershot;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

class CrawlerService
{
    protected const TOTAL_CRAWL_LIMIT = 5000;

    protected const DEFAULT_TIMEOUT_LIMIT = 30;

    protected const DEFAULT_ALLOW_REDIRECTS = true;

    protected const DEFAULT_VERIFY_SSL = true;

    protected const DEFAULT_DELAY_BETWEEN_REQUESTS = 150;

    protected const DEFAULT_CONCURENCY = 1;

    protected const DEFAULT_MAX_RESPONSE_SIZE = 1024 * 1024;

    protected const DEFAULT_PARSEABLE_TYPES = 'text/html,text/plain';

    protected const DEFAULT_SCHEMA = 'https';

    protected Site $site;

    public function __construct(protected SiteRouteRepository $routeRepository, protected Browsershot $browsershot, protected SiteConfigurationRepository $siteConfigRepo)
    {
    }

    public function crawl(Site $site): void
    {
        $this->site = $site;

        $crawler = Crawler::create($this->getCrawlerConfig())
            ->setDefaultScheme(self::DEFAULT_SCHEMA)
            ->setCrawlObserver(new CustomCrawlerObserver($site, $this->routeRepository))
            ->setCrawlProfile(new CustomCrawlProfile($site))
            ->setTotalCrawlLimit(self::TOTAL_CRAWL_LIMIT)
            ->setConcurrency(
                self::DEFAULT_CONCURENCY
            )
            ->setMaximumResponseSize(self::DEFAULT_MAX_RESPONSE_SIZE)
            ->setParseableMimeTypes(
                explode(',', $this->getValue(config('site_settings.broken_routes.mime_types'))  ?? self::DEFAULT_PARSEABLE_TYPES)
            )
            ->setDelayBetweenRequests(
                $this->getValue(config('site_settings.broken_routes.crawler_delay')) ?? self::DEFAULT_DELAY_BETWEEN_REQUESTS
            );

        if ($this->isActive(config('site_settings.broken_routes.nofollow_links'))) {
            $crawler->acceptNofollowLinks();
        }

        if ($this->isActive(config('site_settings.broken_routes.execute_js'))) {
            $crawler->executeJavascript()
                ->setBrowsershot($this->browsershot->noSandbox());
        }

        if ($this->isActive(config('site_settings.broken_routes.respect_robots'))) {
            $crawler->respectRobots();
        } else {
            $crawler->ignoreRobots();
        }

        $crawler->startCrawling($site->uri);
    }

    private function getCrawlerConfig()
    {
        return [
            RequestOptions::ALLOW_REDIRECTS => self::DEFAULT_ALLOW_REDIRECTS,
            RequestOptions::TIMEOUT => self::DEFAULT_TIMEOUT_LIMIT,
            RequestOptions::VERIFY => self::DEFAULT_VERIFY_SSL,
            RequestOptions::HEADERS => [
                'User-Agent' => config('crawler_defaults.user_agent')
            ],
        ];
    }

    public function isActive(string $settingName): bool
    {
        return $this->siteConfigRepo->siteHasSettingActive($this->site, $settingName);
    }

    public function getValue(string $settingName): mixed
    {
        return $this->siteConfigRepo->getSettingValueForSiteByName($this->site, $settingName);
    }
}
