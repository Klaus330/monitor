<?php

namespace App\Repositories;

use App\Enums\SettingGroup;
use App\Models\Setting;
use App\Models\SettingGroup as ModelsSettingGroup;
use App\Models\Site;
use App\Models\SiteConfiguration;
use Illuminate\Database\Eloquent\Collection;

class SiteConfigurationRepository
{
    public function create(array $data): SiteConfiguration
    {
        return SiteConfiguration::create($data);
    }

    public function update(array $data)
    {
        $site = Site::find($data['site_id'])->load('configurations');

        $site->configurations->each(function ($siteSetting) use ($data) {
            $siteSetting->update([
                'value' => $data[$siteSetting->setting->name]
            ]);
        });
    }

    public function getSiteConfigurationsByGroupId(Site $site, int $groupId): Collection
    {
        $groupSettings = Setting::select('id')
            ->where('group_id', $groupId)
            ->get()
            ->pluck('id');

        return $site->configurations()
            ->whereIn('setting_id', $groupSettings)
            ->get();
    }

    public function getSettingValueForSiteByName(Site $site, string $name): mixed
    {
        try {
            $setting = Setting::where('name', $name)
                ->first();

            return SiteConfiguration::forSite($site->id)
                ->where('setting_id', $setting->id)
                ->first()
                ?->value;
        } catch (\Exception $e) {
            dd($e, $site);
        }
    }

    public function siteHasSettingActive(Site $site, string $settingName)
    {
        return $this->getSettingValueForSiteByName($site, $settingName) === "1";
    }


    public function getActiveGroups(Site $site)
    {
        $activeMonitorsNames =  $this->getMonitorsNames($site, true);

        return ModelsSettingGroup::whereIn('name', $activeMonitorsNames)->get();
    }

    public function getInActiveGroups(Site $site)
    {
       $activeMonitorsNames =  $this->getMonitorsNames($site, false);

        return ModelsSettingGroup::whereIn('name', $activeMonitorsNames)->get();
    }

    public function getMonitorsNames(Site $site, bool $areActive)
    {
        return \DB::table('setting_groups')
        ->join('settings', 'setting_groups.id', '=', 'settings.group_id')
        ->join('site_configurations', 'settings.id', '=', 'site_configurations.setting_id')
        ->where('site_configurations.site_id', $site->id)
        ->where('site_configurations.value', $areActive ? "1" : "0")
        ->where('setting_groups.id', (SettingGroup::MONITORS)->id())
        ->pluck('settings.name');
    }
}
