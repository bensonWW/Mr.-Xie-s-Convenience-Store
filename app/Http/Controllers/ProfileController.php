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
            'address' => 'nullable|string',
            'birthday' => 'nullable|date',
        ]);

        $user->update($request->only(['name', 'phone', 'address', 'birthday']));

        return response()->json(['message' => 'Profile updated', 'user' => $user]);
    }
}
