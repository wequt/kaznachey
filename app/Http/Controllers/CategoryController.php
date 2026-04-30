<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
{
    /** @var \App\Models\User $user */
    $user = \Illuminate\Support\Facades\Auth::user();

    $query = $user->categories();

    if ($request->has('type')) {
        $query->where('type', $request->query('type'));
    }

    return response()->json($query->get());
}
}