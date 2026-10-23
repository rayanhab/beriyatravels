<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'offer_id',
        'departure_id',
        'full_name',
        'email',
        'phone',
        'departure_date',
        'travelers',
        'message',
        'status',
        'booking_reference',
        'price_per_traveler_mad',
        'estimated_total_mad',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date:Y-m-d',
            'travelers' => 'integer',
            'price_per_traveler_mad' => 'decimal:2',
            'estimated_total_mad' => 'decimal:2',
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function departure(): BelongsTo
    {
        return $this->belongsTo(Departure::class);
    }
}
