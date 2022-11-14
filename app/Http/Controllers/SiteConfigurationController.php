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
            'configuration' => $site->configuration->only('crawler_delay', 'id', 'respect_robots'),
        ]);
    }

    public function update(Site $site, Request $request, SiteConfigurationRepository $siteConfigurationRepository)
    {
        $validated = $request->validate([
            'crawler_delay' => 'required|numeric|min:0',
            'respect_robots' => 'required|boolean'
        ]);

        try {

            $siteConfigurationRepository->update([
                'site_id' => $site->id,
                ...$validated
            ]);
    
            return back()->with('success', 'Settings saved successfully');
        }catch(QueryException $e)
        {
            return back()->with('error', 'An error occured while updating.');
        }
    }
}
