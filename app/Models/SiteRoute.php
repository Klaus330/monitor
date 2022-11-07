<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'route',
        'http_code',
        'found_on',
        'updated_at'
    ];

    protected $visible = [
        'site_id',
        'route',
        'http_code',
        'found_on',
        'updated_at'
    ];


    public function getUpdatedAtAttribute($value)
    {
        return (new Carbon($value))->diffForHumans();
    }

    public function getRouteAttribute($value)
    {
        return $value . '/';
    }
}
