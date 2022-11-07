<?php

namespace App\Listeners;

use App\Events\SiteRegistered;
use App\Jobs\CrawlSite;
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
    public function __construct(public CrawlerService $crawler)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SiteRegistered  $event
     * @return void
     */
    public function handle(SiteRegistered $event)
    {
        dispatch(new CrawlSite($event->site, $this->crawler))->onQueue('crawlers');
    }
}
