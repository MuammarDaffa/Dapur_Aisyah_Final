<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders', 'total');

        // Filter tab
        if ($request->filled('tab')) {
            if ($request->tab === 'pending') {
                $query->where('status_suspend', 'pending_verification');
            } elseif ($request->tab === 'suspended') {
                $query->where('status_suspend', 'suspended');
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->latest()->paginate(15);

        // Count per status untuk badges
        $pendingCount = User::where('role', 'customer')->where('status_suspend', 'pending_verification')->count();
        $suspendedCount = User::where('role', 'customer')->where('status_suspend', 'suspended')->count();

        return view('admin.customers.index', compact('customers', 'pendingCount', 'suspendedCount'));
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

    /**
     * Tangguhkan akun pelanggan.
     */
    public function suspend(Request $request, User $user)
    {
        abort_unless($user->role === 'customer', 404);

        $user->update([
            'status_suspend' => 'suspended',
        ]);

        return back()->with('success', "Akun {$user->name} berhasil ditangguhkan.");
    }

    /**
     * Aktifkan kembali akun pelanggan (verifikasi & hapus flag suspend).
     */
    public function activate(Request $request, User $user)
    {
        abort_unless($user->role === 'customer', 404);

        $user->update([
            'status_suspend' => 'active',
            'old_phone' => null, // Reset old_phone setelah diverifikasi
        ]);

        return back()->with('success', "Akun {$user->name} berhasil diaktifkan kembali.");
    }
}
