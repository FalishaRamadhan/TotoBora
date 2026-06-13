<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrowthMeasurement extends Model
{
    protected $primaryKey = 'measurement_id';

    protected $fillable = [
        'child_id',
        'date_measured',
        'weight_kg',
        'height_cm',
        'who_weight_status',
        'who_height_status',
        'worker_id',
    ];

    protected $casts = [
        'date_measured' => 'date',
        'weight_kg'     => 'decimal:2',
        'height_cm'     => 'decimal:2',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class, 'child_id', 'child_id');
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id', 'id');
    }

    public function isFlagged(): bool
    {
        return in_array($this->who_weight_status, ['At Risk', 'Underweight'])
            || in_array($this->who_height_status, ['At Risk', 'Stunted']);
    }

    public function weightBadge(): array
    {
        return match($this->who_weight_status) {
            'Underweight' => ['label' => 'Underweight', 'class' => 'bg-red-100 text-red-700'],
            'At Risk'     => ['label' => 'At Risk',     'class' => 'bg-amber-100 text-amber-700'],
            default       => ['label' => 'Normal',      'class' => 'bg-green-100 text-green-700'],
        };
    }

    public function heightBadge(): array
    {
        return match($this->who_height_status) {
            'Stunted'  => ['label' => 'Stunted',  'class' => 'bg-red-100 text-red-700'],
            'At Risk'  => ['label' => 'At Risk',  'class' => 'bg-amber-100 text-amber-700'],
            default    => ['label' => 'Normal',   'class' => 'bg-green-100 text-green-700'],
        };
    }
}