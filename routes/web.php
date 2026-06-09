<?php

use App\Http\Controllers\AccountCategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    Route::get('/directories', [AccountCategoryController::class, 'index'])->name('directories.index');
    Route::post('/directories/accounts', [AccountCategoryController::class, 'storeAccount'])->name('accounts.store');
    Route::post('/directories/transfer', [AccountCategoryController::class, 'transfer'])->name('accounts.transfer');
    Route::put('/directories/accounts/{account}', [AccountCategoryController::class, 'updateAccount'])->name('accounts.update');
    Route::post('/directories/categories', [AccountCategoryController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/directories/accounts/{account}', [AccountCategoryController::class, 'destroyAccount'])->name('accounts.destroy');
    Route::delete('/directories/categories/{category}', [AccountCategoryController::class, 'destroyCategory'])->name('categories.destroy');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleBlock'])->name('admin.users.toggle');
});

require __DIR__ . '/settings.php';