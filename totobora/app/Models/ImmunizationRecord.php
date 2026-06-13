<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ImmunizationRecord extends Model
{
    protected $primaryKey = 'record_id';

    protected $fillable = [
        'child_id',
        'vaccine_name',
        'dose_number',
        'date_administered',
        'next_due_date',
        'worker_id',
        'notes',
    ];

    protected $casts = [
        'date_administered' => 'date',
        'next_due_date'     => 'date',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class, 'child_id', 'child_id');
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id', 'id');
    }

    public function isOverdue(): bool
    {
        return $this->next_due_date && $this->next_due_date->isPast();
    }
}