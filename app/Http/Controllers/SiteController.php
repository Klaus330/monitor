<?php

namespace App\Http\Controllers;

use App\Events\SiteRegistered;
use App\Jobs\CrawlersWatcher;
use App\Models\Site;
use App\Models\SiteRoute;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\SiteRepository;
use App\Repositories\SiteRouteRepository;
use App\Rules\ValidWebsite;
use App\Services\CrawlerService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class SiteController extends Controller
{

    public function __construct(protected SiteRepository $siteRepository)
    {
        
    }

    public function create(User $user, Request $request, SiteRepository $repo): RedirectResponse
    {
        $validated = $request->validate([
            'site'  => ['required', new ValidWebsite]
        ]);

        $domainName = preg_replace('/https?:\/\//', '', $validated['site']);

        $url = 'https://' . $domainName;

        try {
            $site = $repo->create([
                'user_id' => $user->id,
                'url' => $url
            ]);

            SiteRegistered::dispatch($site);

            session()->flash('success', 'Site added successfully.');

            return back();
        } catch (QueryException $e) {
            dd($e);
            session()->flash('error', 'An error occured.');
            
            return back()->withErrors(['site' => 'Site already registered']);
        }
    }


    public function show(Site $site, SiteRouteRepository $siteRouteRepository)
    {
        $routes = $siteRouteRepository
                        ->latestSiteRouteStatuses($site)
                        ->paginate(30);


        return inertia("Sites/Show", [
            'site' => $site,
            'siteRoutes' => $routes,
        ]);
    }

    public function test()
    {
        // $site = Site::find(1);
        // dd([
        //     'site' => $site->only('name'),
        //     'configuration' => $site->configuration->only('crawler_delay', 'id')
        // ]);
        (new CrawlersWatcher(new CrawlerService(new SiteRouteRepository())))->handle();
        // return I1nertia::modal("Components/CustomModal");
    }

    public function destroy(Request $request)
    {
        // Add authorization

        $validated = $request->validate(['site_id' => "required|integer"]);

        $site = $this->siteRepository->find($validated['site_id']);

        $site->delete();

        session()->flash('success', 'Site deleted successfully.');

        return redirect(route('dashboard'));
    }
}
