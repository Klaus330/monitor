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
        'updated_at'
    ];

    public $visible = [
        'id',
        'crawler_delay'
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
