<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuotationRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class QuotationController extends Controller
{
    public function store(StoreQuotationRequest $request): JsonResponse
    {
        $ages = array_map('intval', array_filter(array_map('trim', explode(',', $request->validated('age')))));

        $start = Carbon::parse($request->validated('start_date'))->startOfDay();
        $end = Carbon::parse($request->validated('end_date'))->startOfDay();
        $tripDays = $start->diffInDays($end) + 1;

        $total = 0.0;
        foreach ($ages as $age) {
            $total += 3 * $this->ageLoad($age) * $tripDays;
        }

        return response()->json([
            'total' => round($total, 2),
            'currency_id' => $request->validated('currency_id'),
            'quotation_id' => random_int(1, 2_147_483_647),
        ]);
    }

    private function ageLoad(int $age): float
    {
        return match (true) {
            $age >= 18 && $age <= 30 => 0.6,
            $age >= 31 && $age <= 40 => 0.7,
            $age >= 41 && $age <= 50 => 0.8,
            $age >= 51 && $age <= 60 => 0.9,
            default => 1.0,
        };
    }
}
