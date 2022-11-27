<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Cache;

class MonitorsPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function runBrokenLinksMonitor(User $user, Site $site): bool
    {
        $cacheKey = auth()->user()->id . $site->id . 'broken_links';

        return Cache::get($cacheKey, true);
    }
}
