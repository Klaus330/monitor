<?php

namespace App\Jobs;

use App\Models\Site;
use App\Repositories\SiteConfigurationRepository;
use App\Repositories\SiteRepository;
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
    public function __construct(protected CrawlerService $crawler, protected SiteConfigurationRepository $siteConfigRepo, protected SiteRepository $siteRepository)
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
        $this->siteRepository
            ->getSitesWithActiveCrawlers()
            ->chunk(100, function ($sites) {
                $sites->each(function ($site) {
                    $lastCrawledAt = $site->latestCrawledAt();
                    Log::info("Attempting to crawl site with id:" . $site->id);

                    if (
                        !$this->siteConfigRepo->siteHasSettingActive($site, config('site_settings.monitors.broken_routes'))
                        || $lastCrawledAt->diffInDays() < self::CRAWLER_DELAY
                    ) {
                        Log::info("Failed. Diff in days:" . $lastCrawledAt->diffInDays());
                        Log::info("Failed. Has crawling monitor active:" . (string) $this->siteConfigRepo->siteHasSettingActive($site, config('site_settings.monitors.broken_routes')));
                        return;
                    }

                    Log::info("Will crawl site with id:" . $site->id . ', ' . $lastCrawledAt);
                    CrawlSite::dispatch($site, $this->crawler, $this->siteConfigRepo)->onQueue('crawlers');
                });
            });
    }
}
