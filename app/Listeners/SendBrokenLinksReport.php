<?php

namespace App\Listeners;

use App\Events\SiteCrawled;
use App\Jobs\SendBrokenLinksReportMail;
use App\Repositories\SiteRouteRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBrokenLinksReport
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(protected SiteRouteRepository $routeRepository)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SiteCrawled $event)
    {
        if(!$this->routeRepository->hasBrokenLinks($event->site->id))
        {
            return;
        }

        dispatch(new SendBrokenLinksReportMail($event->site, $this->routeRepository))->onQueue('mails');
    }
}
