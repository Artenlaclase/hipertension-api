<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infusion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'benefits',
        'preparation',
        'precaution_level',
        'precaution_note',
        'category',
        'recommended_ml',
        'max_daily_cups',
        'image_url',
    ];

    protected $casts = [
        'recommended_ml' => 'integer',
        'max_daily_cups' => 'integer',
    ];

    /* ── Relaciones ─────────────────────────────────────────────── */

    public function hydrationLogs()
    {
        return $this->hasMany(HydrationLog::class);
    }

    /* ── Scopes ─────────────────────────────────────────────────── */

    public function scopeSafe($query)
    {
        return $query->where('precaution_level', 'safe');
    }

    public function scopeCaution($query)
    {
        return $query->where('precaution_level', 'caution');
    }

    public function scopeAvoid($query)
    {
        return $query->where('precaution_level', 'avoid');
    }
}
