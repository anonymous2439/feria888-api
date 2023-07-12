<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coin;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class CoinsController extends Controller
{
    public function loadCoins(Request $request) {
        $from = Auth::user();
        $user = User::find($request->input('user_id'));
        $amount = $request->input('amount');

        if (!$user || !$amount) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $latest_coin = $user->coins()->latest()->first();
        $new_balance = ($latest_coin->coin_balance ?? 0) + $amount;

        $coin = new Coin();
        $coin->user_id = $user->id;
        $coin->coin_balance = $new_balance;
        $coin->save();

        // save the transaction log
        $transaction = new Transaction();
        $transaction->from = $from->id; 
        $transaction->to = $user->id; 
        $transaction->type = 'wallet_to_coins'; 
        $transaction->amount = $amount; 
        $transaction->save();

        return response()->json($coin, 200);
    }

    public function index()
    {
        $coins = Coin::all();
        return response()->json($coins);
    }

    public function store(Request $request)
    {
        $coin = new Coin();
        $coin->amount = $request->input('amount');
        $coin->save();

        return response()->json($coin, 201);
    }

    public function show($id)
    {
        $coin = Coin::find($id);

        if (!$coin) {
            return response()->json(['message' => 'Coin not found'], 404);
        }

        return response()->json($coin);
    }

    public function update(Request $request, $id)
    {
        $coin = Coin::find($id);

        if (!$coin) {
            return response()->json(['message' => 'Coin not found'], 404);
        }

        $coin->amount = $request->input('amount');
        $coin->save();

        return response()->json($coin);
    }

    public function destroy($id)
    {
        $coin = Coin::find($id);

        if (!$coin) {
            return response()->json(['message' => 'Coin not found'], 404);
        }

        $coin->delete();

        return response()->json(['message' => 'Coin deleted']);
    }
}
