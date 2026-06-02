<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'clinic_id',
        'name',
        'cpf_cnpj',
        'birth_date',
        'phone',
        'email',
        'zip_code',
        'street',
        'neighborhood',
        'number',
        'complement',
        'city',
        'state',
        'is_active',
        'odontogram_state',
        'responsible_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'birth_date' => 'date',
            'is_active' => 'boolean',
            'odontogram_state' => 'array',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }

    public function patientSchedules(): HasMany
    {
        return $this->hasMany(PatientSchedule::class);
    }

    public function responsible()
    {
        return $this->belongsTo(Customer::class, 'responsible_id');
    }
}
