<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'branch_id',
        'role',
        'status',
        'phone',
        'permissions',
        'settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'permissions' => 'array',
            'settings' => 'array',
        ];
    }

    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'role' => $this->role,
        ];
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Helper methods
    public function isAppAdmin()
    {
        return $this->role === 'app_admin';
    }

    public function isCompanyAdmin()
    {
        return $this->role === 'company_admin';
    }

    public function isBranchUser()
    {
        return $this->role === 'branch_user';
    }

    public function canAccessCompany($companyId)
    {
        return $this->isAppAdmin() || $this->company_id == $companyId;
    }

    public function canAccessBranch($branchId)
    {
        return $this->isAppAdmin() || 
               ($this->isCompanyAdmin() && $this->company_id == Branch::find($branchId)?->company_id) || 
               $this->branch_id == $branchId;
    }
}
