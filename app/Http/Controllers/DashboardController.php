<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $userId = Auth::id();

        $totalBalance = Account::where('user_id', $userId)->sum('balance');

        $recentTransactions = Transaction::where('transactions.user_id', $userId)
            ->with(['category', 'account'])
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

        return Inertia::render('Dashboard', [
            'totalBalance' => (float)$totalBalance,
            'recentTransactions' => $recentTransactions,
            'monthStats' => $monthStats
        ]);
    }
}