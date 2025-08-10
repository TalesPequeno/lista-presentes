<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'category',
        'is_reserved',
        'reserved_by',
        'observation',
        'reserved_at',
    ];

    protected $casts = [
        'is_reserved' => 'boolean',
        'reserved_at' => 'datetime', // vira Carbon automaticamente
    ];
}