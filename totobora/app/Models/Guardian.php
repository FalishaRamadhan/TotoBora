<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $primaryKey = 'guardian_id';
    protected $fillable = ['child_id','first_name','last_name','phone_number','email','relationship'];

    public function child()     { return $this->belongsTo(Child::class, 'child_id', 'child_id'); }
    public function reminders() { return $this->hasMany(Reminder::class, 'guardian_id', 'guardian_id'); }
}
