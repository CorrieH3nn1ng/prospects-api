<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'email',
        'phone',
        'address',
        'manager_name',
        'status',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getFullNameAttribute()
    {
        return $this->company->name . ' - ' . $this->name;
    }
}
