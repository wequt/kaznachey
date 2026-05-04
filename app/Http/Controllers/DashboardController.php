<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $totalBalance = $user->accounts()->sum('balance');

        $monthlyIncome = $user->transactions()
            ->whereHas('category', fn($q) => $q->where('type', 'income'))
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');

        $monthlyExpenses = $user->transactions()
            ->whereHas('category', fn($q) => $q->where('type', 'expense'))
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_balance' => $totalBalance,
                'monthly_income' => $monthlyIncome,
                'monthly_expense' => $monthlyExpenses,
            ]
        ]);
    }
}
