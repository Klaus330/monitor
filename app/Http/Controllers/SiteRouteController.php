<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteRoute;
use App\Repositories\SiteRouteRepository;
use Illuminate\Http\Request;

class SiteRouteController extends Controller
{
    public function show(Site $site, SiteRoute $route, SiteRouteRepository $routesRepo)
    {
        $routes = $routesRepo->findRouteHistoryFor($site->id, $route->route);
        return $routes;
    }

    public function brokenRoutes(Site $site, SiteRouteRepository $siteRouteRepository)
    {
        $brokenRoutes = $siteRouteRepository->findBrokenRoutesForSite($site->id)->paginate(30);
        $siteRoutes = $siteRouteRepository
            ->latestSiteRouteStatuses($site)
            ->paginate(15);

        $siteRoutesCount = $siteRouteRepository->getRoutesCountFor($site->id);


        return inertia("SiteRoutes/Broken", compact("brokenRoutes", 'site', 'siteRoutes', 'siteRoutesCount'));
    }

    public function getAllRoutes(Site $site, SiteRouteRepository $siteRouteRepository)
    {
        return  $siteRouteRepository
            ->latestSiteRouteStatuses($site)
            ->get();
    }
}
