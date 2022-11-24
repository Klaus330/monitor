<?php

namespace App\Models;

use App\Enums\SettingGroup;
use App\Enums\SiteState;
use App\Enums\State;
use App\Repositories\SiteConfigurationRepository;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\UriInterface;

class Site extends Model
{
    use HasFactory;

    protected const SECURE_HTTP = "https";
    protected const PENDING_STATE = 'pending';
    protected const VERB_GET = 'GET';
    protected const VERB_POST = 'POST';
    protected const OVERDUE_LIMIT = '5';

    public $fillable = [
        "url",
        "user_id",
        "status",
        "next_run",
        "emailed_at",
        "verb",
        "payload",
        "check",
        "timeout",
        "downtime",
        "name",
        "headers",
        'state',
    ];

    public $casts = [
        "payload" => "array",
        "headers" => "array",
        'robots_preference' => 'array',
        'sitemaps' => 'array'
    ];

    public $visible = [
        "id",
        'name',
        'url',
        'user_id',
        'status',
        'routes',
        'inactive_monitors',
        'configurations',
    ];

    public $appends = [
        'inactive_monitors'
    ];

    public function getStatus(): State
    {
        if ($this->status === self::PENDING_STATE) {
            return State::PENDING;
        }

        return boolval(preg_match("/2\d{2}/", $this->status)) ? State::SUCCESS : State::ERROR;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getHeadersAttribute($value)
    {
        if ($value === null) {
            return $value;
        }

        return (array) json_decode($value);
    }

    public function schedulers()
    {
        return $this->hasMany(Scheduler::class);
    }

    public function hasSchedulers(): State
    {
        return $this->schedulers()->count() > 0 ? State::SUCCESS : State::ERROR;
    }

    public function getHost()
    {
        preg_match_all(
            "/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?([a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?)/",
            $this->url,
            $matches
        );

        return count($matches[2]) > 0 ? $matches[2][0] : $this->url;
    }

    public function isUp(): bool
    {
        return preg_match("/2\d{2}/", $this->status);
    }

    public function isDown(): bool
    {
        return !$this->isUp();
    }

    public function isPending()
    {
        return $this->status === self::PENDING_STATE;
    }

    public function getSslCertificateStatus(): State
    {
        if (!$this->hasSslCertificate()) {
            return State::PENDING;
        }

        $ssl = $this->sslCertificate;

        if ($ssl->validTo->lt(now())) {
            return State::ERROR;
        }

        return $this->sslCertificate->validTo->lt(now()->addDays($ssl->expires)) ? State::INFO : State::SUCCESS;
    }

    public function hasSslCertificate(): bool
    {
        return $this->sslCertificate()->exists();
    }

    public function sslCertificate()
    {
        return $this->hasOne(SslCertificate::class, "site_id");
    }

    public function stats()
    {
        return $this->hasMany(SiteStats::class, "site_id")->latest();
    }

    public function getLatestStatsAttribute()
    {
        return $this->stats()->where('created_at', '>=', now()->subDay())->get();
    }

    public function isSecured()
    {
        return strtolower($this->schema) === self::SECURE_HTTP;
    }

    public function getHostAttribute()
    {
        $parsedUrl = parse_url($this->url);
        return $parsedUrl["host"];
    }

    public function getSchemaAttribute()
    {
        $parsedUrl = parse_url($this->url);
        return $parsedUrl["scheme"];
    }

    public function getNameAttribute()
    {
        $friendlyName = \DB::table('site_configurations')
                    ->join('settings', "site_configurations.setting_id", '=', 'settings.id')
                    ->where('settings.name', 'friendly_name')
                    ->where('site_configurations.site_id', $this->id)
                    ->first()
                    ->value;
        
        if (empty($friendlyName)) {
            return $this->url;
        }

        return $friendlyName;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function isOwner($user)
    {
        return $this->owner->id === $user->id;
    }

    public function getUrlAttribute($value)
    {
        return trim($value, '/');
    }

    public function getDomainNameAttribute()
    {
        return preg_replace('/https?:\/\//', '', $this->url);
    }

    public function acceptsGet()
    {
        return $this->method === self::VERB_GET;
    }

    public function hasCheckString()
    {
        return !empty($this->check);
    }

    public function validateResponse($responseBody)
    {
        return preg_match("/{$this->check}/", $responseBody);
    }

    public function allowedToSendEmail()
    {
        return $this->emailed_at === null || now()->diffInHours($this->emailed_at) > 1;
    }

    public function hasTimeout()
    {
        return $this->timeout != 0;
    }

    public function scopeLastStatsOverdue($query)
    {
        $limit = self::OVERDUE_LIMIT;
        $query->whereRaw("DATE_SUB(next_run, INTERVAL {$limit} MINUTE) > (SELECT ended_at from site_stats where site_id = sites.id order by ended_at limit 1)");
    }

    public function getLastMonthMonitoringInfo()
    {
        $latestStats = $this->stats()->where('http_code', 'not like', '3__')->get()->groupBy(function ($item) {
            return $item->created_at->format('d');
        })->map(function ($collection) {
            return $collection->first();
        })->flatten()->take(30);

        $array = [];
        foreach ($latestStats as $stats) {
            $array[now()->day - $stats->ended_at->day] = $stats;
        }
        $latestStats = $array;

        if (!array_key_exists(0, $latestStats) && array_key_exists(1, $latestStats)) {
            $latestStats[0] = $latestStats[1];
        }

        return collect($latestStats);
    }

    public function getLastIncidentsAttribute()
    {
        return $this->stats()
            ->where('http_code', 'not like', '2__')
            ->where('http_code', 'not like', '3__')
            ->take(10)
            ->get();
    }

    public function getLastIncidentAttribute()
    {
        if ($this->last_incidents->isEmpty()) {
            return null;
        }

        return $this->last_incidents[0];
    }

    public function routes()
    {
        return $this->hasMany(SiteRoute::class, 'site_id');
    }

    public function getRoutesArrayAttribute()
    {
        return array_map(function ($el) {
            return $el['route'];
        }, $this->routes()->select('route')->get()->toArray());
    }


    public function getBrokenLinksAttribute()
    {
        return $this->routes()
            ->where('http_code', 'not like', '2__')
            ->where('http_code', 'not like', '3__');
    }

    public function hasBrokenLinks()
    {
        return $this->broken_links->get()->isNotEmpty();
    }

    public function brokenLinksStatus()
    {
        return $this->broken_links->get()->isEmpty() ? State::SUCCESS : State::ERROR;
    }

    public function getDirReportsAttribute()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/reports/' . $this->id . '/';
    }


    public function getAveragePerformanceAttribute(): int
    {
        $stats = $this->latest_stats->take(30);

        return (int) ($stats->reduce(function ($carry, $item) {
            return $carry + $item->duration;
        }) / count($stats));
    }

    public function scopeSslOutdated($query)
    {
        $query->whereRaw('DATE_ADD((SELECT ssl_certificates.updated_at FROM ssl_certificates WHERE sites.id = ssl_certificates.site_id), INTERVAL 1 DAY) <= CURRENT_DATE');
    }

    public function getDirLighthouseReportsAttribute()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/lighthouse/' . $this->id . '/';
    }

    public function hasLighthouseReport()
    {
        return file_exists($this->dir_lighthouse_reports . 'report.html');
    }

    public function latestCrawled()
    {
        $lastCrawledDate = $this->routes()
            ->select(\DB::raw('DISTINCT MAX(updated_at) as last_updated'))
            ->groupBy('route')
            ->orderBy('last_updated', "DESC")
            ->limit(1)
            ->pluck('last_updated');

        if ($lastCrawledDate->isEmpty()) {
            return now();
        }

        return new Carbon(
            $lastCrawledDate[0]
        );
    }

    public function configurations()
    {
        return $this->hasMany(SiteConfiguration::class, 'site_id');
    }


    public function getSitemapsAttribute(string $value): array
    {
        return explode(',', json_decode($value));
    }

    public function getUriAttribute(): UriInterface
    {
        return new Uri($this->url);
    }

    public function getInactiveMonitorsAttribute()
    {
        return resolve(SiteConfigurationRepository::class)->getInActiveGroups($this)->pluck('name');
    }

    public function isDraft()
    {
        return $this->state == (SiteState::DRAFT)->id();
    }
}
