<?php

namespace App\Http\Controllers;

use App\Events\SiteRegistered;
use App\Jobs\CrawlSite;
use App\Jobs\RegisterRobotsTxtPreferences;
use App\Models\Site;
use App\Repositories\SiteRepository;
use App\Repositories\SiteRouteRepository;
use App\Services\CrawlerService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(SiteRepository $siteRepository, SiteRouteRepository $siteRouteRepository)
    {

        // dd($siteRouteRepository->getRoutesSnapshotsCount(2));
        // dispatch(new CrawlSite(Site::find(14s), new CrawlerService(new SiteRouteRepository)))->onQueue('crawlers');

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
