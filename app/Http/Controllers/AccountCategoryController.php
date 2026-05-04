<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AccountCategoryController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

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
