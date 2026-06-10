<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        abort_unless($user->role === 'customer', 404);

        $orders = $user->orders()
            ->with('cateringService')
            ->latest()
            ->paginate(10);

        return view('admin.customers.show', compact('user', 'orders'));
    }
}
