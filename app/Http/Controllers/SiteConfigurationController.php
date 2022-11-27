<?php

namespace App\Http\Controllers;

use App\Enums\SettingGroup as EnumsSettingGroup;
use App\Enums\SiteState;
use App\Events\SettingsPreconfigured;
use App\Models\SettingGroup;
use App\Models\Site;
use App\Repositories\SiteConfigurationRepository;
use App\Rules\ValidContentType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SiteConfigurationController extends Controller
{
    public function index(Site $site, SiteConfigurationRepository $siteConfigurationRepository)
    {
        $settings = $site->configurations->load('setting');
        $formValues = $settings->map(function ($siteSetting) {
            return [
                $siteSetting->setting->name => $siteSetting->value
            ];
        });

        $inActiveMonitors = $siteConfigurationRepository->getInActiveGroups($site)->pluck('id');

        $groupedSettings = $settings->map(function ($siteSetting) {
            return [
                'display_name' => $siteSetting->setting->display_name,
                'name' => $siteSetting->setting->name,
                'value_type' => $siteSetting->setting->value_type,
                'value' => $siteSetting->value,
                'group_id' => $siteSetting->setting->group_id,
                'description' => $siteSetting->setting->description
            ];
        })->filter(function ($siteSetting) use ($inActiveMonitors) {
            return !\in_array($siteSetting['group_id'], $inActiveMonitors->toArray());
        })->groupBy(function ($setting) {
            return $setting['group_id'];
        });

        $settingGroups = SettingGroup::all()->map(function ($item) {
            return [
                'display_name' => $item->display_name,
                'name' => $item->name
            ];
        })->filter(function ($item, $index) use ($inActiveMonitors) {
            return !\in_array($index + 1, $inActiveMonitors->toArray());
        });

        return inertia('Settings/Index', [
            'site' => $site,
            'settingGroups' => $settingGroups,
            'configuration' => $groupedSettings,
            'formValues' => $formValues
        ]);
    }

    public function update(Site $site, Request $request, SiteConfigurationRepository $siteConfigurationRepository)
    {
        $validated = $request->validate([
            "friendly_name" => 'nullable|string',
            "crawler_delay" => 'nullable|numeric|min:0',
            "respect_robots" => 'nullable|boolean',
            "execute_js" => 'nullable|boolean',
            "nofollow_links" => 'nullable|boolean',
            "mime_types" => ['nullable', new ValidContentType()],
            "something" => "nullable|string",
            "broken_routes" => 'nullable|boolean',
            "lighthouse" => 'nullable|boolean',
        ]);

        try {
            $siteConfigurationRepository->update([
                'site_id' => $site->id,
                ...$validated
            ]);

            $site->update(['state' => (SiteState::ACTIVE)->id()]);

            if ($request->has('preconfigure')) {
                SettingsPreconfigured::dispatch($site);

                return redirect(route('site.show', compact('site')))->with('success', 'Settings saved successfully');
            }

            return redirect(route('site.show', ['site' => $site->id]))->with('success', 'Settings saved successfully');
        } catch (QueryException $e) {
            return redirect(route('site.configuration', ['site' => $site->id]))->with('error', 'An error occured while updating.');
        }
    }
}
