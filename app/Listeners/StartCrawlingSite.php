<?php

namespace App\Listeners;

use App\Events\SettingsPreconfigured;
use App\Jobs\CrawlSite;
use App\Jobs\RegisterRobotsTxtPreferences;
use App\Repositories\SiteConfigurationRepository;
use App\Services\CrawlerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StartCrawlingSite
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(public CrawlerService $crawler, protected SiteConfigurationRepository $siteConfigRepo)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SettingsPreconfigured  $event
     * @return void
     */
    public function handle(SettingsPreconfigured $event)
    {
        if (!$this->siteConfigRepo->siteHasSettingActive($event->site, config('site_settings.monitors.broken_routes'))) {
            return;
        }

        dispatch(new CrawlSite($event->site, $this->crawler, $this->siteConfigRepo))->onQueue('crawlers');
    }
}
