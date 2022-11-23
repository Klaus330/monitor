<?php

namespace App\Listeners;

use App\Models\Setting;
use App\Repositories\SiteConfigurationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateSiteConfiguration
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(protected SiteConfigurationRepository $configurationRepo)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $settings = Setting::all();

        foreach ($settings as $setting)
        {
            $this->configurationRepo->create([
                'site_id' => $event->site->id,
                'setting_id' => $setting->id,
                'value' => $setting->default_value
            ]);
        }
    }
}
