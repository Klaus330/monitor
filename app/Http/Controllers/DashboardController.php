<?php

namespace App\Http\Controllers;

use App\Events\SiteRegistered;
use App\Jobs\CrawlSite;
use App\Models\Site;
use App\Repositories\SiteRepository;
use App\Repositories\SiteRouteRepository;
use App\Services\CrawlerService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(SiteRepository $siteRepository)
    {
        dispatch(new CrawlSite(Site::find(1), new CrawlerService(new SiteRouteRepository())))->onQueue('crawlers');

        $sites = $siteRepository->findSitesForUser(auth()->user()->id)->paginate(10);

        return Inertia::render('Dashboard', [
            'sites' => $sites
        ]);
    }
}
