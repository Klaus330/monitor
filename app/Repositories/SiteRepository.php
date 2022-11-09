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
}