<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Repositories\SiteConfigurationRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SiteConfigurationController extends Controller
{
    public function index(Site $site)
    {
        return inertia('Settings/Index', [
            'site' => $site,
            'configuration' => $site->configuration->only(
                'crawler_delay',
                'id',
                'respect_robots',
                'has_uptime',
                'has_crawlers',
                'has_lighthouse'
            ),
        ]);
    }

    public function update(Site $site, Request $request, SiteConfigurationRepository $siteConfigurationRepository)
    {
        $validated = $request->validate([
            'crawler_delay' => 'required|numeric|min:0',
            'respect_robots' => 'required|boolean',
            'has_crawlers' => 'required|boolean',
            'has_uptime' => 'required|boolean',
            'has_lighthouse' => 'required|boolean'
        ]);

        try {

            $siteConfigurationRepository->update([
                'site_id' => $site->id,
                ...$validated
            ]);

            return redirect(route('site.configuration', ['site' => $site->id]))->with('success', 'Settings saved successfully');
        } catch (QueryException $e) {
            return redirect(route('site.configuration', ['site' => $site->id]))->with('error', 'An error occured while updating.');
        }
    }
}
