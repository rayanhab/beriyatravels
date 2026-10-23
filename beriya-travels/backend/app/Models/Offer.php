<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    protected $fillable = [
        'destination_id',
        'title',
        'category',
        'summary',
        'badge',
        'duration',
        'includes',
        'price_mad',
        'program',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_mad' => 'decimal:2',
            'is_active' => 'boolean',
            'program' => 'array',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
