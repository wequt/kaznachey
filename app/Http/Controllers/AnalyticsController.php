<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = Auth::id();

        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->endOfMonth()->toDateString());

        $getStatsByType = function ($type) use ($userId, $dateFrom, $dateTo) {
            return Transaction::where('transactions.user_id', $userId)
                ->join('categories', 'transactions.category_id', '=', 'categories.id')
                ->where('categories.type', $type)
                ->whereBetween('transactions.transaction_date', [$dateFrom, $dateTo])
                ->select('categories.name', DB::raw('SUM(transactions.amount) as total'))
                ->groupBy('categories.name')
                ->orderBy('total', 'desc')
                ->get();
        };

        $expensesStats = $getStatsByType('expense');
        $incomeStats = $getStatsByType('income');

        $totalsData = Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->whereBetween('transactions.transaction_date', [$dateFrom, $dateTo])
            ->select('categories.type', DB::raw('SUM(transactions.amount) as sum'))
            ->groupBy('categories.type')
            ->get()
            ->pluck('sum', 'type');

        $incomeTotal = $totalsData['income'] ?? 0;
        $expenseTotal = $totalsData['expense'] ?? 0;

        return Inertia::render('kazna/AnalyticsPage', [
            'expensesStats' => $expensesStats,
            'incomeStats' => $incomeStats,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ],
            'totals' => [
                'income' => (float)$incomeTotal,
                'expense' => (float)$expenseTotal,
                'profit' => (float)($incomeTotal - $expenseTotal)
            ]
        ]);
    }
}