<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientSchedule extends Model
{
    protected $fillable = [
        'clinic_id',
        'customer_id',
        'schedule_date',
        'procedure',
        'notes',
        'status',
    ];

    protected $casts = [
        'schedule_date' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}