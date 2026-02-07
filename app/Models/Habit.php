<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function habitLogs()
    {
        return $this->hasMany(HabitLog::class);
    }
}
