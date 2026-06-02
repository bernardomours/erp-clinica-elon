<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $fillable = [
        'name', 
        'tax_id', 
        'slug', 
        'expires_at',
        'signature_status',
        'zip_code',
        'street',
        'neighborhood',
        'number',
        'complement',
        'city',
        'state',
        'has_scheduling',
        'has_financial',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
        ];
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->signature_status === 'blocked') {
            return false;
        }

        if (!$this->expires_at) {
            return true;
        }

        return now()->startOfDay()->lte($this->expires_at);
    }

    public function currentMonthExpenses()
    {
        return $this->hasMany(Expense::class)
                    ->whereBetween('due_date', [now()->startOfMonth(), now()->endOfMonth()])
                    ->orderBy('due_date', 'desc');
    }

    public function currentMonthRevenues()
    {
        return $this->hasMany(Revenue::class) 
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->orderBy('created_at', 'desc');
    }
}
