<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'user_id',
        'client_id',
        'contact_person',
        'contact_email',
        'phone',
        'contact_designation',
        'type_of_business',
        'call_type_id',
        'area_of_work',
        'services',
        'followup_date',
        'has_drc_office',
        'client_interest_level',
        'client_mood',
        'potential_value_id',
        'client_satisfaction_level',
        'status',
        'scheduled_date',
        'actions_required',
        'opportunities',
        'routes_challenges',
        'call_notes',
        'documents',
        'inco_terms'
    ];

    protected $casts = [
        'services' => 'array',
        'has_drc_office' => 'boolean',
        'client_interest_level' => 'integer',
        'client_mood' => 'integer',
        'client_satisfaction_level' => 'integer',
        'scheduled_date' => 'datetime',
        'followup_date' => 'date',
        'actions_required' => 'array',
        'opportunities' => 'array',
        'routes_challenges' => 'array',
        'call_notes' => 'array',
        'documents' => 'array'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
