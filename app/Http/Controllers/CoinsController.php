<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coin;

class CoinsController extends Controller
{
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
