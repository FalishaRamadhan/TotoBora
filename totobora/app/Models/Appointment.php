<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $primaryKey = 'appointment_id';

    protected $fillable = [
        'child_id',
        'scheduled_date',
        'vaccine_due',
        'status',
        'worker_id',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class, 'child_id', 'child_id');
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id', 'id');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'appointment_id', 'appointment_id');
    }

    public function getStatusBadge(): array
    {
        return match($this->status) {
            'attended' => ['label' => 'Attended', 'class' => 'bg-green-100 text-green-700'],
            'missed'   => ['label' => 'Missed',   'class' => 'bg-red-100 text-red-700'],
            default    => ['label' => 'Scheduled','class' => 'bg-blue-100 text-blue-700'],
        };
    }

    public function isOverdue(): bool
    {
        return $this->status === 'scheduled'
            && $this->scheduled_date->isPast();
    }
}