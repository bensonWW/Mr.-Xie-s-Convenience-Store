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

        $now = Carbon::now();

        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            return response()->json(['message' => 'Coupon is not active yet'], 400);
        }

        if ($coupon->ends_at && $now->gt($coupon->ends_at)) {
            return response()->json(['message' => 'Coupon has expired'], 400);
        }

        if ($coupon->limit_price && $request->total_amount < $coupon->limit_price) {
            return response()->json(['message' => "Minimum spend of {$coupon->limit_price} required"], 400);
        }

        $discount = 0;
        if ($coupon->type === 'fixed') {
            $discount = $coupon->discount_amount;
        } elseif ($coupon->type === 'percentage') {
            $discount = $request->total_amount * ($coupon->discount_amount / 100);
        }

        // Ensure discount doesn't exceed total amount
        $discount = min($discount, $request->total_amount);

        return response()->json([
            'code' => $coupon->code,
            'discount_amount' => $discount,
            'type' => $coupon->type,
            'message' => 'Coupon applied successfully'
        ]);
    }
}
