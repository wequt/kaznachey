<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Budget;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $userId = Auth::id();

        $totalBalance = Account::where('user_id', $userId)->sum('balance');

        $recentTransactions = Transaction::where('transactions.user_id', $userId)
            ->with(['category', 'account', 'destinationAccount'])
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();

        $monthStats = Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('categories.type', 'expense')
            ->whereMonth('transactions.transaction_date', now()->month)
            ->whereYear('transactions.transaction_date', now()->year)
            ->select('categories.name', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.name')
            ->get();
        $currentMonth = now()->format('Y-m');

        $overBudgets = Budget::where('user_id', $userId)
            ->where('budget_month', $currentMonth)
            ->get()
            ->filter(function ($budget) use ($userId, $currentMonth) {
                $spent = Transaction::where('user_id', $userId)
                    ->where('category_id', $budget->category_id)
                    ->where('transaction_date', 'like', "$currentMonth%")
                    ->sum('amount');

                return $spent > $budget->limit_amount;
            })
            ->map(fn($budget) => $budget->category->name)
            ->values()
            ->toArray();

        return Inertia::render('Dashboard', [
            'totalBalance' => (float)$totalBalance,
            'recentTransactions' => $recentTransactions,
            'monthStats' => $monthStats,
            'overBudgets' => $overBudgets
        ]);
    }
}
