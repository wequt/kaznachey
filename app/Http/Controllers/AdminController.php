<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('kazna/AdminDashboard', [
            'stats' => [
                'usersCount' => User::count(),
                'transactionsCount' => Transaction::count(),
                'totalVolume' => Transaction::sum('amount') ?? 0,
            ],
            'users' => User::select('id', 'name', 'email', 'role', 'is_blocked')
                ->withCount('transactions')
                ->get()
        ]);
    }

    public function toggleBlock(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Нельзя заблокировать себя');
        }

        $user->update(['is_blocked' => !$user->is_blocked]);
        return back();
    }
}