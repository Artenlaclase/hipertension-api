<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationAlarm extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id',
        'alarm_time',
        'days_of_week',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }
}
