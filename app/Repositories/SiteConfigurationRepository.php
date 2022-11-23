<?php

namespace App\Repositories;

use App\Models\Setting;
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
                ->value;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function siteHasSettingActive(Site $site, string $settingName)
    {
        return $this->getSettingValueForSiteByName($site, $settingName) === "1";
    }
}
