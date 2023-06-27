<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\User;

class WalletsController extends Controller
{
    public function loadWallet(Request $request)
    {
        $user = User::find($request->input('user_id'));

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $latest_wallet = $user->wallets()->latest()->first();
        $new_balance = ($latest_wallet->wallet_balance ?? 0) + $request->input('amount');

        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->wallet_balance = $new_balance;
        $wallet->save();

        return response()->json($wallet, 200);
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
