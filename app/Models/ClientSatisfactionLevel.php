<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSatisfactionLevel extends Model
{
    protected $fillable = [
        'name',
        'description',
        'value',
        'color'
    ];

    protected $casts = [
        'value' => 'integer'
    ];
}