<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;

class WalletsController extends Controller
{
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
