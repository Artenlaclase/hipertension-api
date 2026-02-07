<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HydrationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'infusion_id',
        'amount_ml',
        'logged_at',
        'notes',
    ];

    protected $casts = [
        'amount_ml' => 'integer',
        'logged_at' => 'datetime',
    ];

    /* ── Relaciones ─────────────────────────────────────────────── */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function infusion()
    {
        return $this->belongsTo(Infusion::class);
    }
}
