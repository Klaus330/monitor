<?php

namespace App\Http\Controllers;

use App\Models\SettingGroup;
use App\Models\Site;
use App\Repositories\SiteConfigurationRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SiteConfigurationController extends Controller
{
    public function index(Site $site)
    {
        $settings = $site->configurations->load('setting');
        $formValues = $settings->map(function ($siteSetting) {
            return [
                $siteSetting->setting->name => $siteSetting->value
            ];
        });

        $groupedSettings = $settings->map(function ($siteSetting) {
            return [
                'display_name' => $siteSetting->setting->display_name,
                'name' => $siteSetting->setting->name,
                'value_type' => $siteSetting->setting->value_type,
                'value' => $siteSetting->value,
                'group_id' => $siteSetting->setting->group_id
            ];
        })->groupBy(function ($setting) {
            return $setting['group_id'];
        });

        return inertia('Settings/Index', [
            'site' => $site,
            'settingGroups' => SettingGroup::all()->pluck('name'),
            'configuration' => $groupedSettings,
            'formValues' => $formValues
        ]);
    }

    public function update(Site $site, Request $request, SiteConfigurationRepository $siteConfigurationRepository)
    {
        $validated = $request->validate([
            "friendly_name" => 'nullable|string',
            "crawler_delay" => 'required|numeric|min:0',
            "respect_robots" => 'required|boolean',
            "execute_js" => 'required|boolean',
            "nofollow_links" => 'required|boolean',
            "mime_types" => "",
            "something" => "nullable|string",
            "broken_routes" => 'required|boolean',
            "lighthouse" => 'required|boolean',
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
