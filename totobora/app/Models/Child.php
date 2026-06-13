<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $primaryKey = 'child_id';
    protected $fillable = ['first_name','last_name','date_of_birth','gender','birth_weight','facility_id','unique_child'];
    protected $casts = [
        'date_of_birth' => 'date',
        'birth_weight'  => 'decimal:2',
    ];

    public function facility()   
    { 
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id'); 
    }

    public function guardians()  
    { 
        return $this->hasMany(Guardian::class, 'child_id', 'child_id'); 
    }

    public function immunizations() 
    { 
        return $this->hasMany(ImmunizationRecord::class, 'child_id', 'child_id'); 
    }

    public function appointments()  
    { 
        return $this->hasMany(Appointment::class, 'child_id', 'child_id'); 
    }

    public function growthMeasurements() 
    { 
        return $this->hasMany(GrowthMeasurement::class, 'child_id', 'child_id'); 
    }

    // age in months
    public function getAgeInMonths(): int 
    {
        return (int) now()->diffInMonths($this->date_of_birth);
    }

    // Human readable: "4 mo" or "2 yr 3 mo"
    public function getAgeLabel(): string
    {
        $months = $this->getAgeInMonths();
        if ($months < 24) {
            return $months . ' mo';
        }
        $years = intdiv($months, 12);
        $rem   = $months % 12;
        return $rem > 0 ? "{$years} yr {$rem} mo" : "{$years} yr";
    }
}
