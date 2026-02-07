<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'topic',
        'level',
        'order',
        'is_premium',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'order'      => 'integer',
    ];
}
