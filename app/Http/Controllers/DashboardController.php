<?php

namespace App\Http\Controllers;

use App\CrawlProfiles\CustomCrawlProfile;
use App\Enums\SettingGroup;
use App\Events\SiteRegistered;
use App\Jobs\CrawlSite;
use App\Jobs\RegisterRobotsTxtPreferences;
use App\Models\Setting;
use App\Models\Site;
use App\Observers\CustomCrawlerObserver;
use App\Repositories\SiteConfigurationRepository;
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
    public function index(SiteRepository $siteRepository, SiteConfigurationRepository $configRepo)
    {
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
