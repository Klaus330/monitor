<?php

namespace App\Repositories;

use App\Models\SiteRoute;

class SiteRouteRepository
{
    public const SNAPSHOT_LIMIT = 3;
    public const INTERVAL_LIMIT = 1;

    public function create($data)
    {
        return SiteRoute::create($data);
    }

    public function routeForSiteExists(int $siteId, $route)
    {
        return SiteRoute::where("site_id", $siteId)
            ->where('route', $route)
            ->whereRaw("DATE_ADD(updated_at, INTERVAL " . self::INTERVAL_LIMIT . " MINUTE) > CURRENT_TIMESTAMP")
            ->exists();
    }


    public function routeHasReachedSnapshotLimit($siteId, $route)
    {
        // SELECT COUNT(route)  FROM site_routes WHERE site_id = 2 and route = "" GROUP BY route;

        return SiteRoute::select("route", \DB::raw("COUNT(*) as route_count"))
            ->where('site_id', $siteId)
            ->where('route', $route)
            ->groupBy('route')
            ->having("route_count", self::SNAPSHOT_LIMIT)
            ->exists();
    }

    public function findLastModifiedSnapshot($siteId, $route)
    {
        // SELECT * FROM site_routes where site_id = 2 and route = "" ORDER BY updated_at LIMIT 1;

        return SiteRoute::where('site_id', $siteId)
            ->where("route", $route)
            ->orderBy('updated_at')
            ->first();
    }

    public function findAllFor(int $site)
    {
        return SiteRoute::where("site_id", $site)->get();
    }
}
