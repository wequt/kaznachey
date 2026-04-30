<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated) {
            $transaction = Transaction::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'account_id' => $validated['account_id'],
                'category_id' => $validated['category_id'],
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'],
            ]);

            $account = Account::find($validated['account_id']);
            $category = \App\Models\Category::find($validated['category_id']);

            if ($category->type === 'expense') {
                $account->balance -= $validated['amount'];
            } else {
                $account->balance += $validated['amount'];
            }

            $account->save();

            return response()->json([
                'message' => 'Транзакция успешно проведена',
                'new_balance' => $account->balance
            ]);
        });
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        $transactions = $user->transactions()
            ->with(['account', 'category'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(15);

        return response()->json($transactions);
    }
}
