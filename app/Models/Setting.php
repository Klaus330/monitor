<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillables = [
        'id',
        'group_id',
        'name',
        'default_value',
        'display_name',
        'description',
        'value_type',
        'updated_at',
    ];

    protected $visible = [
        'id',
        'group_id',
        'name',
        'default_value',
        'display_name',
        'descri',
        'value_type',
        'updated_at',
    ];
}
