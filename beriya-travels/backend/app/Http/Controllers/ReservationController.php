<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Departure;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'offer_id' => [
                'required',
                'integer',
                Rule::exists('offers', 'id')->where('is_active', true),
            ],
            'departure_id' => [
                'required',
                'integer',
                Rule::exists('departures', 'id')->where(fn ($query) => $query
                    ->where('offer_id', $request->input('offer_id'))
                    ->where('departure_date', $request->input('departure_date'))
                    ->where('status', 'open')),
            ],
            'full_name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'departure_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'travelers' => ['required', 'integer', 'min:1', 'max:9'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $offer = Offer::query()
            ->whereKey($validated['offer_id'])
            ->where('is_active', true)
            ->firstOrFail();
        $departure = Departure::query()
            ->whereKey($validated['departure_id'])
            ->where('offer_id', $offer->id)
            ->whereDate('departure_date', $validated['departure_date'])
            ->where('status', 'open')
            ->firstOrFail();
        $priceInCents = (int) round((float) $offer->price_mad * 100);
        $pricePerTraveler = number_format($priceInCents / 100, 2, '.', '');
        $estimatedTotal = number_format(
            ($priceInCents * $validated['travelers']) / 100,
            2,
            '.',
            ''
        );
        $bookingReference = 'BT-' . Str::upper((string) Str::uuid());

        $reservation = Reservation::query()->create([
            ...$validated,
            'status' => 'pending',
            'departure_id' => $departure->id,
            'booking_reference' => $bookingReference,
            'price_per_traveler_mad' => $pricePerTraveler,
            'estimated_total_mad' => $estimatedTotal,
        ]);

        return response()->json([
            'message' => 'Votre demande de réservation a bien été reçue. Nous vous contacterons pour confirmer les détails.',
            'reservation_id' => $reservation->id,
            'booking_reference' => $reservation->booking_reference,
            'offer_title' => $offer->title,
            'departure_date' => $departure->departure_date->format('Y-m-d'),
            'travelers' => $reservation->travelers,
            'price_per_traveler_mad' => $reservation->price_per_traveler_mad,
            'estimated_total_mad' => $reservation->estimated_total_mad,
        ], 201);
    }
}
