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
        if ($request->has('member_level')) {
            // Set member_level_id via MemberLevel model lookup
            $level = \App\Models\MemberLevel::where('slug', $request->member_level)->first();
            if ($level) {
                $user->member_level_id = $level->id;
            }
        }
        if ($request->has('is_level_locked')) $user->is_level_locked = $request->boolean('is_level_locked');

        $user->save();

        return $user;
    }

    public function walletTransaction(Request $request, $id, WalletService $walletService)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|in:deposit,withdraw,adjustment',
            'description' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);

        // For adjustments, amount can be negative (debit) or positive (credit)
        // For deposit/withdraw, amount must be positive
        $amount = $request->amount;
        if ($request->type !== 'adjustment' && $amount <= 0) {
            return response()->json(['message' => 'Amount must be greater than 0 for deposit/withdraw'], 422);
        }

        // Use bcmul for precision when converting to cents
        $amountInCents = (int) bcmul((string) $amount, '100', 0);

        try {
            if ($request->type === 'deposit') {
                $walletService->deposit(
                    $user,
                    $amountInCents,
                    $request->description
                );
            } elseif ($request->type === 'withdraw') {
                $walletService->withdraw(
                    $user,
                    abs($amountInCents), // Ensure positive for withdraw method
                    $request->description
                );
            } elseif ($request->type === 'adjustment') {
                $walletService->adjust(
                    $user,
                    $amountInCents, // Can be positive or negative
                    $request->description
                );
            }

            $user->refresh();
            return response()->json([
                'message' => 'Wallet updated successfully',
                'balance' => $user->balance,
                'type' => $request->type
            ]);
        } catch (\App\Exceptions\InsufficientBalanceException $e) {
            return response()->json(['message' => '餘額不足'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Update user role (admin, staff, customer)
     */
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|in:admin,staff,customer',
        ]);

        // Prevent demoting yourself
        if ($user->id === $request->user()->id && $request->role !== 'admin') {
            return response()->json(['message' => '無法降級自己的權限'], 400);
        }

        $oldRole = $user->role;
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => "角色已從 {$oldRole} 更新為 {$request->role}",
            'user' => $user,
        ]);
    }
}
