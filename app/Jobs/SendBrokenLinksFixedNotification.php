<?php

namespace App\Jobs;

use App\Mail\BrokenLinksFixed;
use App\Models\Site;
use App\Repositories\SiteRouteRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBrokenLinksFixedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected Site $site, protected array $brokenLinks, protected SiteRouteRepository $siteRouteRepository)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->site->user)
            ->send(new BrokenLinksFixed(
                $this->site,
                $this->siteRouteRepository
                    ->getFixedBrokenRoutes($this->site->id, $this->brokenLinks)                
            ));
    }
}
