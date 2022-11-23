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

        $site->configurations->each(function($siteSetting) use ($data) {
            $siteSetting->update([
                'value' => $data[$siteSetting->setting->name]
            ]);
        });
    }

    // public function updateConfigurationFor(int $siteId, string $configurationName)
    // {

    // }

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
}
