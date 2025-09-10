<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PotentialValue extends Model
{
    protected $fillable = [
        'name',
        'description',
        'min_value',
        'max_value',
        'color'
    ];

    protected $casts = [
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2'
    ];
}