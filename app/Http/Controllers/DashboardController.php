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
        $sites = $siteRepository->findSitesForUser(auth()->user()->id);

        return Inertia::render('Dashboard', [
            'sites' => $sites
        ]);
    }
}
