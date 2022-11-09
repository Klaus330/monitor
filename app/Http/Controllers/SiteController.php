<?php

namespace App\Http\Controllers;

use App\Events\SiteRegistered;
use App\Models\Site;
use App\Models\SiteRoute;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\SiteRepository;
use App\Repositories\SiteRouteRepository;
use App\Rules\ValidWebsite;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class SiteController extends Controller
{
    public function create(User $user, Request $request, SiteRepository $repo): RedirectResponse
    {
        $request->validate([
            'site'  => ['required', new ValidWebsite]
        ]);

        try {
            $site = $repo->create([
                'user_id' => $user->id,
                'url' => $request->get('site')
            ]);

            SiteRegistered::dispatch($site);

            return back()->with('success', 'Site added');
        } catch (QueryException $e) {
            return back()->withErrors(['site' => 'Site already registered']);
        }
    }


    public function show(Site $site, SiteRouteRepository $siteRouteRepository)
    {
        $routes = $siteRouteRepository
                        ->latestSiteRouteStatuses($site)
                        ->paginate();

        return inertia("Sites/Show", [
            'site' => $site,
            'siteRoutes' => $routes,
        ]);
    }

    public function test()
    {
        return Inertia::modal("Components/CustomModal");
    }
}
