<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'week_start',
        'notes',
    ];

    protected $casts = [
        'week_start' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
