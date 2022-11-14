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
        'crawler_delay',
        'updated_at',
        'respect_robots'
    ];

    public $visible = [
        'id',
        'crawler_delay',
        'respect_robots'
    ];

    public $casts = [
        'respect_robots' => "boolean",
    ];

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }


    public function getCrawlerDelayInMiliseconds()
    {
        return $this->crawler_delay / 1000;
    }
}
