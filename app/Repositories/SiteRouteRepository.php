<?php

namespace App\Repositories;

use App\Models\Site;
use App\Models\SiteRoute;
use Illuminate\Support\Collection;

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
        return SiteRoute::forSite($siteId)
            ->where('route', $route)
            ->whereRaw("DATE_ADD(updated_at, INTERVAL " . self::INTERVAL_LIMIT . " MINUTE) > CURRENT_TIMESTAMP")
            ->exists();
    }


    public function routeHasReachedSnapshotLimit(int $siteId, string $route)
    {
        return SiteRoute::select("route", \DB::raw("COUNT(*) as route_count"))
            ->forSite($siteId)
            ->where('route', $route)
            ->groupBy('route')
            ->having("route_count", self::SNAPSHOT_LIMIT)
            ->exists();
    }

    public function getRoutesSnapshotsCount(int $siteId)
    {
        return SiteRoute::select("route", \DB::raw("COUNT(route) as route_count"))
            ->forSite($siteId)
            ->groupBy('route')
            ->get()
            ->mapWithKeys(function ($route) {
                return [
                    $route->route => $route->route_count
                ];
            });
    }

    public function findLastModifiedSnapshot(int $siteId, string $route)
    {
        return SiteRoute::forSite($siteId)
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
            ->orderBy('updated_at', 'DESC')
            ->whereIn('updated_at', $latestRoutes);
    }

    public function findBrokenRoutesForSite(int $siteId)
    {
        $latestRoutes = $this->getLatestUpdatedRoutes($siteId);

        return SiteRoute::forSite($siteId)
            ->orderBy('updated_at', 'DESC')
            ->whereIn('updated_at', $latestRoutes)
            ->where(function ($query) {
                return $query->where('http_code', "LIKE", '4__')
                             ->orWhere('http_code', "LIKE", '5__');
            });
    }

    public function hasBrokenLinks(int $siteId)
    {
        return $this->findBrokenRoutesForSite($siteId)->exists();
    }

    public function getBrokenLinksCount(int $siteId): int
    {
        return $this->findBrokenRoutesForSite($siteId)->count();
    }

    public function getLatestUpdatedRoutes(int $siteId)
    {
        return  SiteRoute::forSite($siteId)
            ->select('route', \DB::raw('MAX(updated_at) as updated_at'))
            ->groupBy('route')
            ->orderBy('route')
            ->pluck('updated_at');
    }

    public function findRouteHistoryFor(int $siteId, string $route)
    {
        return SiteRoute::forSite($siteId)
            ->where('route', trim($route, '/'))
            ->get();
    }

    public function getRoutesCountFor(int $siteId): int
    {
        return $this->latestSiteRouteStatuses(Site::find($siteId))->count();
    }

    public function generateCsvReportWithBrokenLinks(Site $site)
    {
        $content = null;
        try {
            $fd = fopen('php://temp/maxmemory:1048576', 'w');

            if ($fd == false) {
                return null;
            }

            $headers = ['route', 'http_code', 'found_on'];

            $routes = $this->findBrokenRoutesForSite($site->id)
                ->select(...$headers)
                ->get()
                ->toArray();

            fputcsv($fd, $headers);

            foreach ($routes as $route) {
                unset($route['last_check']);
                fputcsv($fd, $route);
            }
            rewind($fd);

            $content = stream_get_contents($fd);
        } finally {
            fclose($fd);
        }

        return $content;
    }

    public function getFixedBrokenRoutes(int $siteId, array $brokenRoutes): Collection
    {
        $latestRoutes = $this->getLatestUpdatedRoutes($siteId);

        return SiteRoute::forSite($siteId)
            ->whereIn('route', $brokenRoutes)
            ->orderBy('updated_at', 'DESC')
            ->whereIn('updated_at', $latestRoutes)
            ->where(function ($query) {
                return $query->where('http_code', "LIKE", '2__')
                             ->orWhere('http_code', "LIKE", '3__');
            })
            ->get();
    }
}
