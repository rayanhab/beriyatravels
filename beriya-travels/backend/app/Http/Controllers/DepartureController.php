<?php

namespace App\Http\Controllers;

use App\Models\Departure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DepartureController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        [$year, $month] = array_map('intval', explode('-', $validated['month']));
        if ($month < 1 || $month > 12) {
            throw ValidationException::withMessages([
                'month' => ['Le mois demandé doit être compris entre 01 et 12.'],
            ]);
        }

        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = date('Y-m-t', strtotime($start));

        return response()->json(
            Departure::query()
                ->with([
                    'offer:id,destination_id,title,category,summary,badge,duration,includes,price_mad,program',
                    'offer.destination:id,name,slug,image_url,image_alt',
                ])
                ->where('status', 'open')
                ->whereDate('departure_date', '>=', now()->toDateString())
                ->whereBetween('departure_date', [$start, $end])
                ->whereHas('offer', fn ($query) => $query->where('is_active', true))
                ->whereHas('offer.destination', fn ($query) => $query->where('is_active', true))
                ->orderBy('departure_date')
                ->orderBy('offer_id')
                ->get([
                    'id',
                    'offer_id',
                    'departure_date',
                    'return_date',
                    'status',
                    'note',
                ])
        );
    }
}
