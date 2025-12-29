<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json(['message' => 'Invalid coupon code'], 404);
        }

        $totalAmount = (int) $request->total_amount;

        // Use model's isValidFor() which checks dates, limit_price, AND usage_limit
        if (!$coupon->isValidFor($totalAmount)) {
            // Provide specific error messages
            $now = Carbon::now();
            if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
                return response()->json(['message' => 'Coupon is not active yet'], 400);
            }
            if ($coupon->ends_at && $now->gt($coupon->ends_at)) {
                return response()->json(['message' => 'Coupon has expired'], 400);
            }
            if ($coupon->limit_price && $totalAmount < $coupon->limit_price) {
                return response()->json(['message' => "Minimum spend of {$coupon->limit_price} required"], 400);
            }
            if ($coupon->usage_limit !== null && $coupon->usage_count >= $coupon->usage_limit) {
                return response()->json(['message' => 'Coupon usage limit reached'], 400);
            }
            return response()->json(['message' => 'Coupon is not valid'], 400);
        }

        // Use model's calculateDiscount method which uses bcmath for precision
        $discount = $coupon->calculateDiscount($totalAmount);

        return response()->json([
            'code' => $coupon->code,
            'discount_amount' => $discount,
            'type' => $coupon->type,
            'message' => 'Coupon applied successfully'
        ]);
    }
    public function index(Request $request)
    {
        // Admin gets all coupons
        if ($request->user() && $request->user()->role === 'admin') {
            return response()->json(Coupon::all());
        }

        // Users get only active coupons
        $now = Carbon::now();
        return response()->json(Coupon::where(function ($query) use ($now) {
            $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
        })->where(function ($query) use ($now) {
            $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
        })->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'discount_amount' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percentage',
            'limit_price' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if (isset($validated['ends_at'])) {
            $validated['ends_at'] = Carbon::parse($validated['ends_at'])->endOfDay();
        }

        $coupon = Coupon::create($validated);
        return response()->json($coupon, 201);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|string|unique:coupons,code,' . $id,
            'discount_amount' => 'sometimes|numeric|min:0',
            'type' => 'sometimes|in:fixed,percentage',
            'limit_price' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if (isset($validated['ends_at'])) {
            $validated['ends_at'] = Carbon::parse($validated['ends_at'])->endOfDay();
        }

        $coupon->update($validated);
        return response()->json($coupon);
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return response()->json(['message' => 'Coupon deleted successfully']);
    }
}
