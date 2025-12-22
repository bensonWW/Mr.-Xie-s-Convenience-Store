<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get user wallet info.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $balance = $this->walletService->getBalance($user);

        // Retrieve recent transactions
        $transactions = $user->walletTransactions()
            ->latest()
            ->take(10) // Limit to last 10 for dashboard
            ->get();

        return response()->json([
            'balance' => $balance,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Deposit funds (Mock Implementation).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        // Frontend sends dollars, we convert to cents
        $amount = (int) ($request->input('amount') * 100);
        $description = $request->input('description', 'Manual Deposit');

        try {
            $transaction = $this->walletService->deposit($user, $amount, $description);

            return response()->json([
                'message' => 'Deposit successful',
                'balance' => $this->walletService->getBalance($user),
                'transaction' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
