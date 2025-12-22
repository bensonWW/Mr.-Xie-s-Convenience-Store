<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string', // Backward compatibility for legacy string address
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'detail_address' => 'nullable|string',
            'birthday' => 'nullable|date',
        ]);

        // Update User Basic Info
        $user->update($request->only(['name', 'phone', 'birthday']));

        // Handle Address Logic
        if ($request->has('city') && $request->has('district') && $request->has('detail_address')) {
            // Create or Update Default Address
            $recipientName = $request->input('recipient_name', $user->name);
            $phone = $request->input('phone', $user->phone);

            $address = $user->addresses()->where('is_default', true)->first();

            if ($address) {
                $address->update([
                    'city' => $request->city,
                    'district' => $request->district,
                    'zip_code' => $request->zip_code,
                    'detail_address' => $request->detail_address,
                    'recipient_name' => $recipientName,
                    'phone' => $phone
                ]);
            } else {
                $user->addresses()->create([
                    'city' => $request->city,
                    'district' => $request->district,
                    'zip_code' => $request->zip_code,
                    'detail_address' => $request->detail_address,
                    'recipient_name' => $recipientName,
                    'phone' => $phone,
                    'is_default' => true
                ]);
            }

            // Backfill legacy address column for compatibility
            $fullAddress = $request->zip_code . ' ' . $request->city . $request->district . $request->detail_address;
            $user->update(['address' => $fullAddress]);
        } elseif ($request->has('address')) {
            $user->update(['address' => $request->address]);
        }

        return response()->json(['message' => 'Profile updated', 'user' => $user->load('addresses')]);
    }
}
