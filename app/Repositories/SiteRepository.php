<?php

namespace App\Repositories;

use App\Models\Site;

class SiteRepository 
{
    public function create($data) 
    {
        return Site::create($data);
    }

    public function findSitesForUser($user)
    {
        $user = $user->id ?? $user;

        return Site::where('user_id', $user);
    }

    public function find(int $id)
    {
        return Site::find($id);
    }

    public function getSitesWithActiveCrawlers()
    {
        return Site::leftJoin('site_configurations', 'site_configurations.site_id', '=', 'sites.id')
                    ->leftJoin('settings', "site_configurations.setting_id", '=', 'settings.id')
                    ->where('settings.name', 'broken_routes')
                    ->where('site_configurations.value', '1')
                    ->orderBy('site_configurations.site_id')
                    ->select('sites.*');
    }
}