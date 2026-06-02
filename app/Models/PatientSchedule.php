<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientSchedule extends Model
{
    protected $fillable = [
        'clinic_id',
        'customer_id',
        'procedure_id',
        'tooth_number',
        'schedule_date',
        'notes',
        'status',
        'clinical_evolution',
        'clinical_photos',
    ];

    protected $casts = [
        'schedule_date' => 'datetime',
        'clinical_photos' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }
}