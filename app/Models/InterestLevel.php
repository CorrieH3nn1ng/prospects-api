<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterestLevel extends Model
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
