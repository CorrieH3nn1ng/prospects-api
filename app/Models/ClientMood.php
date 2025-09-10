<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientMood extends Model
{
    protected $fillable = [
        'name',
        'description',
        'emoji',
        'value',
        'color'
    ];

    protected $casts = [
        'value' => 'integer'
    ];
}
