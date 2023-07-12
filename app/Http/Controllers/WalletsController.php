<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class WalletsController extends Controller
{
    public function loadWallet(Request $request)
    {
        $from = Auth::user();
        $user = User::find($request->input('user_id'));

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $latest_wallet = $user->wallets()->latest()->first();
        $amount = $request->input('amount');

        // validates to accept only amount greater than 0
        if ($amount <= 0) {
            return response()->json(['message' => 'Amount must be greater than 0'], 200);
        }
        $new_balance = ($latest_wallet->wallet_balance ?? 0) + $amount;

        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->wallet_balance = $new_balance;
        $wallet->save();

        // save the transaction log
        $transaction = new Transaction();
        $transaction->from = $from->id; 
        $transaction->to = $user->id; 
        $transaction->type = 'wallet_load'; 
        $transaction->amount = $amount; 
        $transaction->save();

        return response()->json($wallet, 200);
    }

    public function deductWallet(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $latest_wallet = $user->wallets()->latest()->first();
        $amount = $request->input('amount');

        // validates to accept only amount greater than 0
        if ($amount <= 0) {
            return response()->json(['message' => 'Amount must be greater than 0', 'success' => 0], 200);
        }
        $new_balance = ($latest_wallet->wallet_balance ?? 0) - $amount;

        if ($new_balance < 0) {
            return response()->json(['message' => 'Not enough wallet balance', 'success' => 0], 200);
        }

        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->wallet_balance = $new_balance;
        $wallet->save();

        return response()->json(['wallet' => $wallet, 'success' => 1], 200);
    }

    public function index()
    {
        $wallets = Wallet::all();
        return response()->json($wallets);
    }

    public function store(Request $request)
    {
        $wallet = new Wallet();
        $wallet->balance = $request->input('balance');
        $wallet->save();

        return response()->json($wallet, 201);
    }

    public function show($id)
    {
        $wallet = Wallet::find($id);

        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found'], 404);
        }

        return response()->json($wallet);
    }

    public function update(Request $request, $id)
    {
        $wallet = Wallet::find($id);

        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found'], 404);
        }

        $wallet->balance = $request->input('balance');
        $wallet->save();

        return response()->json($wallet);
    }

    public function destroy($id)
    {
        $wallet = Wallet::find($id);

        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found'], 404);
        }

        $wallet->delete();

        return response()->json(['message' => 'Wallet deleted']);
    }
}
