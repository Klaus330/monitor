<?php

namespace App\Repositories;

use App\Models\SiteRoute;

class SiteRouteRepository
{
    public function create($data)
    {
        return SiteRoute::create($data);
    }

    public function routeForSiteExists(int $siteId, $route)
    {
        return SiteRoute::where("site_id", $siteId)
                        ->where('route', $route)
                        ->exists();
    }

    public function findAllFor(int $site)
    {
        return SiteRoute::where("site_id", $site)->get();
    }
}
