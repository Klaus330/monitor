<?php

namespace App\Jobs;

use App\Models\Site;
use App\Repositories\SiteConfigurationRepository;
use App\Services\CrawlerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CrawlSite implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200;
    public $maxExceptions = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Site $site,
        protected CrawlerService $crawler,
        protected SiteConfigurationRepository $siteConfigRepo
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->siteConfigRepo->siteHasSettingActive($this->site, config('site_settings.monitors.broken_routes'))) {
            return;
        }

        $this->crawler->crawl($this->site);
    }

    public function failed($e)
    {
        Log::error($e);
        dd($e);
    }

    public function uniqueId()
    {
        return $this->site->id . $this->site->url;
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(20);
    }
}
