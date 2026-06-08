<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $transactions = $user->transactions()
            ->with(['account', 'category'])
            ->when($request->input('account_id'), function ($query, $accountId) {
                $query->where('account_id', $accountId);
            })
            ->when($request->input('category_id'), function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->input('date_from'), function ($query, $dateFrom) {
                $query->where('transaction_date', '>=', $dateFrom);
            })
            ->when($request->input('date_to'), function ($query, $dateTo) {
                $query->where('transaction_date', '<=', $dateTo);
            })
            ->when($request->input('type'), function ($query, $type) {
                $query->whereHas('category', function ($q) use ($type) {
                    $q->where('type', $type);
                });
            })
            ->orderBy('transaction_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('kazna/TransactionsPage', [
            'transactions' => $transactions,
            'accounts' => $user->accounts()->get(['id', 'name', 'balance']),
            'categories' => Category::all(['id', 'name', 'type']),
            'filters' => $request->only(['account_id', 'category_id', 'date_from', 'date_to', 'type']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $validated['account_id'],
                'category_id' => $validated['category_id'],
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'],
            ]);

            $account = Account::findOrFail($validated['account_id']);
            $category = Category::findOrFail($validated['category_id']);

            if ($category->type === 'expense') {
                $account->balance -= $validated['amount'];
            } else {
                $account->balance += $validated['amount'];
            }

            $account->save();
        });

        return redirect()->back()->with('success', 'Транзакция успешно проведена');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        DB::transaction(function () use ($transaction) {
            $account = $transaction->account;
            $category = $transaction->category;

            if ($category->type === 'expense') {
                $account->balance += $transaction->amount;
            } else {
                $account->balance -= $transaction->amount;
            }

            $account->save();
            $transaction->delete();
        });

        return redirect()->back()->with('success', 'Операция отменена, баланс обновлен');
    }
}