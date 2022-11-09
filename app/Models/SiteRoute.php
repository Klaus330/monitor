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
        'id',
        'route',
        'http_code',
        'found_on',
        'updated_at'
    ];

    protected $appends = [
        'last_check'
    ];

    public function getLastCheckAttribute()
    {
        return (new Carbon($this->updated_at))->diffForHumans();
    }

    public function getRouteAttribute($value)
    {
        return $value . '/';
    }

    public function getFoundOnAttribute($value)
    {
        return $value . '/';
    }

    public function toArray()
    {
        $array = parent::toArray();
        foreach ($this->getMutatedAttributes() as $key)
        {
            if ( ! array_key_exists($key, $array)) {
                $array[$key] = $this->{$key};   
            }
        }
        return $array;
    }
}
