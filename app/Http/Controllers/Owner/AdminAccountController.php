<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminAccountController extends Controller
{
    /**
     * Menampilkan daftar admin
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->orderBy('created_at', 'desc')->get();
        return view('owner.admins.index', compact('admins'));
    }

    /**
     * Menampilkan form tambah admin
     */
    public function create()
    {
        return view('owner.admins.create');
    }

    /**
     * Menyimpan admin baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        return redirect()->route('owner.admins.index')->with('success', 'Akun admin berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit admin
     */
    public function edit($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        return view('owner.admins.edit', compact('admin'));
    }

    /**
     * Menyimpan perubahan profil admin
     */
    public function update(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('owner.admins.index')->with('success', 'Profil admin berhasil diperbarui.');
    }

    /**
     * Mengubah status aktif/nonaktif admin
     */
    public function toggleStatus($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->is_active = !$admin->is_active;
        $admin->save();

        $statusMessage = $admin->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('owner.admins.index')->with('success', "Akun admin berhasil {$statusMessage}.");
    }

    /**
     * Mereset password admin
     */
    public function updatePassword(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('owner.admins.index')->with('success', 'Password admin berhasil direset.');
    }
}
