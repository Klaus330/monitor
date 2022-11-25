<?php

namespace App\Http\Controllers;

use App\Mail\BrokenLinksReport;
use App\Models\Site;
use App\Repositories\SiteConfigurationRepository;
use App\Repositories\SiteRepository;
use App\Repositories\SiteRouteRepository;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(SiteRepository $siteRepository)
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
