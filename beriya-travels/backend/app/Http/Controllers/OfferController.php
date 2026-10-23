<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\JsonResponse;

class OfferController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Offer::query()
                ->with(['destination:id,name,slug,description,image_url,image_alt'])
                ->where('is_active', true)
                ->whereHas('destination', fn ($query) => $query->where('is_active', true))
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get([
                    'id',
                    'destination_id',
                    'title',
                    'category',
                    'summary',
                    'badge',
                    'duration',
                    'includes',
                    'price_mad',
                    'program',
                ])
        );
    }
}
