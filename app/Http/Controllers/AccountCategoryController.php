<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AccountCategoryController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('kazna/DirectoriesPage', [
            'accounts' => $user->accounts()->latest()->get(),
            'categories' => $user->categories()->latest()->get(),
        ]);
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric',
            'currency' => 'required|string|max:3',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->accounts()->create($validated);

        return redirect()->back();
    }

    public function updateAccount(Request $request, Account $account): RedirectResponse
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $account->update($validated);

        return redirect()->back();
    }

    public function transfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'destination_account_id' => 'required|exists:accounts,id|different:account_id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $sourceAccount = Account::findOrFail($validated['account_id']);
        $destinationAccount = Account::findOrFail($validated['destination_account_id']);

        // Проверка прав собственности
        if ($sourceAccount->user_id !== Auth::id() || $destinationAccount->user_id !== Auth::id()) {
            abort(403);
        }

        // Выполняем атомарную транзакцию в БД
        DB::transaction(function () use ($validated, $sourceAccount, $destinationAccount) {
            $sourceAccount->decrement('balance', $validated['amount']);
            $destinationAccount->increment('balance', $validated['amount']);

            Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $validated['account_id'],
                'destination_account_id' => $validated['destination_account_id'],
                'amount' => $validated['amount'],
                'description' => $validated['description'] ?? 'Перевод между счетами',
                'transaction_date' => now()->format('Y-m-d'),
            ]);
        });

        return redirect()->back()->with('success', 'Перевод успешно выполнен');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'icon' => 'nullable|string|max:255',
        ]);

        if (empty($validated['icon'])) {
            $validated['icon'] = 'folder';
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->categories()->create($validated);

        return redirect()->back();
    }

    public function destroyAccount(Account $account): RedirectResponse
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $account->delete();
        return redirect()->back()->with('success', 'Счет удален');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $category->delete();
        return redirect()->back()->with('success', 'Категория удалена');
    }
}