<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'name',
        'code',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'industry',
        'business_type',
        'employee_count',
        'annual_revenue',
        'primary_contact_name',
        'primary_contact_email',
        'primary_contact_phone',
        'primary_contact_designation',
        'status',
        'notes',
        'tags'
    ];

    protected $casts = [
        'annual_revenue' => 'decimal:2',
        'tags' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
