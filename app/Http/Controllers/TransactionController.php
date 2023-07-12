<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function getUserTransactions(Request $request)
    {
        $user = Auth::user(); // Retrieve the authenticated user
        
        // Retrieve the transactions of the authenticated user
        $transactions = Transaction::with('fromUser')
            ->where('from', $user->id)
            ->orWhere('to', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Format the date and include the 'from' username for each transaction
        $formattedTransactions = $transactions->map(function ($transaction) {
            $transaction->formatted_date = $transaction->created_at->format('Y-m-d H:i:s');
            $transaction->from_username = $transaction->fromUser->username;
            return $transaction;
        });
        
        return response()->json($formattedTransactions);
    }
}
