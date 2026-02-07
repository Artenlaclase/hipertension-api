<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id',
        'taken_at',
        'status',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }
}
