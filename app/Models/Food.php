<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'name',
        'sodium_level',
        'potassium_level',
        'category',
    ];

    public function foodLogs()
    {
        return $this->hasMany(FoodLog::class);
    }
}
