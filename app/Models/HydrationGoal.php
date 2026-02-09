<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class HydrationGoal extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'goal_ml', 'effective_date',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'goal_ml' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
