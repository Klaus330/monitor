<?php

namespace App\Providers;

use App\Events\BrokenLinksFixed;
use App\Events\SettingsPreconfigured;
use App\Events\SiteCrawled;
use App\Events\SiteRegistered;
use App\Listeners\CreateSiteConfiguration;
use App\Listeners\SendBrokenLinksFixedMail;
use App\Listeners\SendBrokenLinksReport;
use App\Listeners\StartCrawlingSite;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SiteRegistered::class => [
            CreateSiteConfiguration::class,
        ],
        SettingsPreconfigured::class => [
            StartCrawlingSite::class,
        ],
        SiteCrawled::class => [
            SendBrokenLinksReport::class,
        ],
        BrokenLinksFixed::class => [
            SendBrokenLinksFixedMail::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
