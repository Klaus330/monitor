<?php

namespace App\Jobs;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class RegisterRobotsTxtPreferences implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected Site $site)
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
        $url = $this->site->url . '/robots.txt';

        $response = Http::withOptions(["verify" => true])->get($url);

        $relatedInfo = collect(explode('User-agent: ', $response->body()))
            ->filter(function ($el) {
                return str_starts_with($el, '*') || str_starts_with($el, 'Whoops');
            });

        $forbiddenRoutes = $relatedInfo
            ->map(function ($el) {
                preg_match_all('/Disallow: (.*)/', $el, $matches);

                return $matches[1];
            })->flatten();

        $siteUrl = preg_replace('/\//', '\/', $this->site->url);

        preg_match_all('/Sitemap: (.*)/', $response->body(), $sitemapMatches);

        $sitemaps = preg_replace("/{$siteUrl}/", '', implode(',', $sitemapMatches[1]));

        $this->site->sitemaps = json_encode($sitemaps);
        $this->site->robots_preference = json_encode($forbiddenRoutes);

        $this->site->save();

        $this->site->configurations->update([
            'respect_robots' => !empty($forbiddenRoutes)
        ]);
    }

    public function failed($e)
    {
        Log::error($e);
        dd($e);
    }

    public function uniqueId()
    {
        return $this->site->id;
    }
}
