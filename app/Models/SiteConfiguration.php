<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteConfiguration extends Model
{
    use HasFactory;

    public $fillable = [
        'id',
        'site_id',
        'setting_id',
        'value',
        'updated_at',
    ];

    public $visible = [
        'id',
        'site_id',
        'setting_id',
        'value',
        'updated_at',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function setting()
    {
        return $this->belongsTo(Setting::class, 'setting_id');
    }

    public function getCrawlerDelayInMiliseconds()
    {
        return $this->crawler_delay / 1000;
    }

    public function scopeForSite($query, int $siteId)
    {
        return $query->where('site_id', $siteId);
    }
}
