<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $primaryKey = 'reminder_id';

    protected $fillable = [
        'appointment_id',
        'guardian_id',
        'send_datetime',
        'channel',
        'delivery_status',
    ];

    protected $casts = [
        'send_datetime' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardian_id', 'guardian_id');
    }

    public function statusBadge(): array
    {
        return match($this->delivery_status) {
            'sent'    => ['label' => 'Sent',    'class' => 'bg-green-100 text-green-700'],
            'failed'  => ['label' => 'Failed',  'class' => 'bg-red-100 text-red-700'],
            default   => ['label' => 'Pending', 'class' => 'bg-amber-100 text-amber-700'],
        };
    }
}