<?php

namespace App\Repositories;

use App\Models\Site;
use App\Models\SiteRoute;

class SiteRouteRepository
{
    public const SNAPSHOT_LIMIT = 3;
    public const INTERVAL_LIMIT = 1;

    public function create(array $data)
    {
        return SiteRoute::create($data);
    }

    public function routeForSiteExists(int $siteId, string $route)
    {
        return SiteRoute::where("site_id", $siteId)
            ->where('route', $route)
            ->whereRaw("DATE_ADD(updated_at, INTERVAL " . self::INTERVAL_LIMIT . " MINUTE) > CURRENT_TIMESTAMP")
            ->exists();
    }


    public function routeHasReachedSnapshotLimit(int $siteId, string $route)
    {
        return SiteRoute::select("route", \DB::raw("COUNT(*) as route_count"))
            ->where('site_id', $siteId)
            ->where('route', $route)
            ->groupBy('route')
            ->having("route_count", self::SNAPSHOT_LIMIT)
            ->exists();
    }

    public function findLastModifiedSnapshot(int $siteId, string $route)
    {
        return SiteRoute::where('site_id', $siteId)
            ->where("route", $route)
            ->orderBy('updated_at')
            ->first();
    }

    public function findAllFor(int $site)
    {
        return SiteRoute::where("site_id", $site)->get();
    }


    public function latestSiteRouteStatuses(Site $site)
    {
        $latestRoutes = $this->getLatestUpdatedRoutes($site->id);

        return SiteRoute::where('site_id', $site->id)
            ->whereIn('updated_at', $latestRoutes);
    }

    public function findBrokenRoutesForSite(int $siteId)
    {
        $latestRoutes = $this->getLatestUpdatedRoutes($siteId);

        return SiteRoute::where('site_id', $siteId)
            ->where('http_code', "NOT LIKE", '2__')
            ->where('http_code', "NOT LIKE", '3__')
            ->whereIn('updated_at', $latestRoutes);
    }

    public function getLatestUpdatedRoutes(int $siteId)
    {
        return  SiteRoute::where('site_id', $siteId)
            ->select('route', \DB::raw('MAX(updated_at) as updated_at'))
            ->groupBy('route')
            ->pluck('updated_at');
    }
}
