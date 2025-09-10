<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'website',
        'business_registration',
        'tax_number',
        'subscription_plan',
        'status',
        'subscription_starts_at',
        'subscription_ends_at',
        'monthly_fee',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'subscription_starts_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
            'monthly_fee' => 'decimal:2',
            'settings' => 'array',
        ];
    }

    // Relationships
    public function branches()
    {
        return $this->hasMany(Branch::class);
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

    public function isSubscriptionActive()
    {
        return $this->isActive() && 
               ($this->subscription_ends_at === null || $this->subscription_ends_at->isFuture());
    }

    public function admins()
    {
        return $this->users()->where('role', 'company_admin');
    }
}
