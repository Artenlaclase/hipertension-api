<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class HydrationRecord extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'liquid_type', 'amount_ml', 'note', 'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'amount_ml' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
