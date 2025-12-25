<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // 'role' => 'string|in:customer,staff,store_member', // Removed for security. Admin/Staff must be seeded.
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', // Always default to customer for public registration
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Invalid login credentials.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if ($user->status === 'banned') {
            throw ValidationException::withMessages([
                'email' => ['Your account has been suspended.'],
            ]);
        }

        // Check last login and enforce re-verification if gap >= 2 days
        $previousLogin = $user->last_login_at;
        $user->last_login_at = now();
        $needsVerification = false;

        if ($previousLogin && $user->last_login_at->diffInDays($previousLogin) >= 2) {
            // Force re-verification by clearing verification timestamp
            $user->email_verified_at = null;
            $needsVerification = true;
        }

        $user->save();

        if ($needsVerification) {
            try {
                app(\App\Services\EmailVerificationService::class)->generateCode($user);
                app(\App\Services\EmailVerificationService::class)->sendCode($user);
            } catch (\Throwable $e) {
                // Ignore mailing failures here; frontend can retry via API
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'needs_verification' => $needsVerification,
        ]);
    }

    public function logout(Request $request)
    {
        // Handle Token-based logout (if present)
        $user = $request->user();
        if ($user && $user->currentAccessToken() && method_exists($user->currentAccessToken(), 'delete')) {
            $user->currentAccessToken()->delete();
        }

        // Handle Session-based logout (SPA)
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function updateProfile(Request $request)
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
