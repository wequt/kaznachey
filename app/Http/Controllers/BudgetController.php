<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BudgetController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $userId = Auth::id();
        $currentMonth = now()->format('Y-m');

        $budgets = Budget::where('user_id', $userId)
            ->where('budget_month', $currentMonth)
            ->with('category')
            ->get()
            ->map(function ($budget) use ($userId) {
                $spent = Transaction::where('user_id', $userId)
                    ->where('category_id', $budget->category_id)
                    ->where('transaction_date', 'like', "$budget->budget_month%")
                    ->sum('amount');

                return [
                    'id' => $budget->id,
                    'category_name' => $budget->category->name,
                    'limit_amount' => (float)$budget->limit_amount,
                    'spent_amount' => (float)$spent,
                    'remaining' => (float)($budget->limit_amount - $spent),
                    'progress' => $budget->limit_amount > 0 
                        ? min(round(($spent / $budget->limit_amount) * 100, 1), 100) 
                        : 0,
                    'is_over_limit' => $spent > $budget->limit_amount
                ];
            });

        $categories = Category::where('type', 'expense')->get();

        return Inertia::render('kazna/BudgetPage', [
            'budgets' => $budgets,
            'categories' => $categories,
            'currentMonth' => $currentMonth
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'limit_amount' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            [
                'user_id' => Auth::id(), 
                'category_id' => $validated['category_id'],
                'budget_month' => now()->format('Y-m')
            ],
            ['limit_amount' => $validated['limit_amount']]
        );

        return back();
    }
}