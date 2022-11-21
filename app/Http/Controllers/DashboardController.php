<?php

namespace App\Http\Controllers;

use App\CrawlProfiles\CustomCrawlProfile;
use App\Events\SiteRegistered;
use App\Jobs\CrawlSite;
use App\Jobs\RegisterRobotsTxtPreferences;
use App\Models\Site;
use App\Observers\CustomCrawlerObserver;
use App\Repositories\SiteRepository;
use App\Repositories\SiteRouteRepository;
use App\Services\CrawlerService;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Browsershot\Browsershot;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlAllUrls;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;
use Spatie\Crawler\CrawlProfiles\CrawlSubdomains;

class DashboardController extends Controller
{
    public function index(SiteRepository $siteRepository, CrawlerService $crawlService, SiteRouteRepository $siteRouteRepository, Browsershot $browsershot)
    {
        // $url = "https://home.ca/";

        // return Crawler::create([
        //     RequestOptions::ALLOW_REDIRECTS => true, RequestOptions::TIMEOUT => 30,
        // ])
        //     ->executeJavascript()
        //     ->setBrowsershot((new Browsershot())->noSandbox())
        //     // ->ignoreRobots()
        //     ->setCrawlObserver(new CustomCrawlerObserver(Site::find(3), new SiteRouteRepository()))
        //     ->setCrawlProfile(new CustomCrawlProfile(Site::find(3)))
        //     ->setTotalCrawlLimit(10)
        //     ->setDelayBetweenRequests(100)
        //     ->startCrawling($url);


        // dd((new CrawlerService($siteRouteRepository, $browsershot))->crawl(Site::find(2)));

        // dd(Uri::parse('https://www.claudiupopa.ro/about#asdasd'));
        // dd((new Uri('https://www.claudiupopa.ro/about#asdasd')));

        // dd($siteRouteRepository->getRoutesSnapshotsCount(2));
        dispatch(new CrawlSite(Site::find(2), $crawlService))->onQueue('crawlers');

        $sites = $siteRepository
            ->findSitesForUser(auth()->user()->id)
            ->paginate(10)
            ->through(function ($site) {
                return [
                    'id' => $site->id,
                    'name' => $site->name,
                    'status' => $site->status,
                    'url' => $site->url,
                    'user_id' => $site->user_id,
                    'has' => [
                        'brokenRoutes' => true
                    ]
                ];
            });

        return Inertia::render('Dashboard', [
            'sites' => $sites
        ]);
    }
}
