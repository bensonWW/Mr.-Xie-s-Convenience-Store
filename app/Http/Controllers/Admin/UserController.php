<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\WalletService;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('orders')
            ->withSum(['orders' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            }], 'total_amount');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        return $query->latest()->paginate(15);
    }

    public function show($id)
    {
        return User::withCount('orders')
            ->withSum(['orders' => function ($query) {
                $query->whereIn('status', ['processing', 'shipped', 'completed']);
            }], 'total_amount')
            ->with(['orders' => function ($query) {
                $query->latest();
            }, 'walletTransactions' => function ($query) {
                $query->latest();
            }])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'name' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->role = 'customer';
        $user->save();

        return $user;
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $id,
            'name' => 'required|string',
        ]);

        $user->email = $request->email;
        $user->name = $request->name;
        if ($request->has('password') && $request->password) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('status')) $user->status = $request->status;
        if ($request->has('member_level')) $user->member_level = $request->member_level;
        if ($request->has('is_level_locked')) $user->is_level_locked = $request->boolean('is_level_locked');

        $user->save();

        return $user;
    }

    public function walletTransaction(Request $request, $id, WalletService $walletService)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:deposit,withdraw',
            'description' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);

        $amountInCents = (int) round($request->amount * 100);

        try {
            // Admin wallet transactions don't have an associated order_id
            if ($request->type === 'deposit') {
                $walletService->deposit(
                    $user,
                    $amountInCents,
                    $request->description
                    // No order_id for admin operations
                );
            } else {
                $walletService->withdraw(
                    $user,
                    $amountInCents,
                    $request->description
                    // No order_id for admin operations
                );
            }
            return response()->json(['message' => 'Wallet updated successfully', 'balance' => $user->balance]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
