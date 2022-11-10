<?php

namespace App\Listeners;

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
        $this->configurationRepo->create([
            'site_id' => $event->site->id
        ]);
    }
}
