<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteRoute;
use App\Repositories\SiteRouteRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SiteRouteController extends Controller
{
    public function __construct(protected SiteRouteRepository $routesRepo)
    {
    }

    public function show(Site $site, SiteRoute $route)
    {
        $routes = $this->routesRepo->findRouteHistoryFor($site->id, $route->route);
        return $routes;
    }

    public function brokenRoutes(Site $site)
    {
        $brokenRoutes = $this->routesRepo->findBrokenRoutesForSite($site->id)->paginate(30);
        $siteRoutes = $this->routesRepo
            ->latestSiteRouteStatuses($site)
            ->paginate(15);

        $siteRoutesCount = $this->routesRepo->getRoutesCountFor($site->id);


        return inertia("SiteRoutes/Broken", compact(
            "brokenRoutes",
            'site',
            'siteRoutes',
            'siteRoutesCount'
        ));
    }

    public function getAllRoutes(Site $site)
    {
        return  $this->routesRepo
            ->latestSiteRouteStatuses($site)
            ->get();
    }

    public function downloadCsvReport(Site $site)
    {
        $filename = "csv_report" . date('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($site) {
            echo $this->routesRepo->generateCsvReportWithBrokenLinks($site);
        }, $filename);
    }
}
