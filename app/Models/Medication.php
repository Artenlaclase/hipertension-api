<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'dosage',
        'instructions',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alarms()
    {
        return $this->hasMany(MedicationAlarm::class);
    }

    public function logs()
    {
        return $this->hasMany(MedicationLog::class);
    }
}
