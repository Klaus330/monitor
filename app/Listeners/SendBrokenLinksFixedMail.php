<?php

namespace App\Listeners;

use App\Jobs\SendBrokenLinksFixedNotification;
use App\Repositories\SiteRouteRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBrokenLinksFixedMail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(protected SiteRouteRepository $siteRouteRepository)
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
        dispatch(
            new SendBrokenLinksFixedNotification(
                $event->site,
                $event->brokenLinks,
                $this->siteRouteRepository
            )
        )->onQueue('mails');
    }
}
