<?php

namespace App\Jobs;

use App\Models\Site;
use App\Repositories\SiteConfigurationRepository;
use App\Services\CrawlerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;

class CrawlersWatcher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected const CRAWLER_DELAY = 1;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected CrawlerService $crawler, protected SiteConfigurationRepository $siteConfigRepo)
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
        Site::chunk(100, function ($sites) {
            $sites->each(function ($site) {
                $route = $site->latestCrawled();

                Log::info("Attempting to crawl site with id:" . $site->id);

                if (
                    !$this->siteConfigRepo->siteHasSettingActive($site, config('site_settings.monitors.broken_routes'))
                    || $route->diffInDays() < self::CRAWLER_DELAY
                ) {
                    Log::info("Failed. Diff in days:" . $route->diffInDays());
                    Log::info("Failed. Has crawling monitor active:" . (string) $this->siteConfigRepo->siteHasSettingActive($site, config('site_settings.monitors.broken_routes')));
                    return;
                }

                Log::info("Will crawl site with id:" . $site->id . ', '. $route->diffInMinutes());
                CrawlSite::dispatch($site, $this->crawler, $this->siteConfigRepo)->onQueue('crawlers');
            });
        });
    }
}
