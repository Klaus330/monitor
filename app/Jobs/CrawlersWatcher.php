<?php

namespace App\Jobs;

use App\Models\Site;
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
    public function __construct(protected CrawlerService $crawler)
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
                $site->load('configuration');

                if (!$site->configuration->has_crawlers || $route->diffInMinutes() <= self::CRAWLER_DELAY) {
                    return;
                }


                CrawlSite::dispatch($site, $this->crawler)->onQueue('crawlers');
            });
        });
    }
}
