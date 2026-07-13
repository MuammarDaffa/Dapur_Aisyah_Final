<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('customer.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|08)[0-9]{8,13}$/'],
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
        ]);

        // Jika user suspended dan mengubah nomor HP → pindah ke pending_verification
        if ($user->isSuspended() && $validated['phone'] !== $user->phone) {
            $validated['old_phone'] = $user->phone;
            $validated['status_suspend'] = 'pending_verification';

            // Kirim notifikasi ke admin pertama (internal database notification)
            $adminId = \App\Models\User::where('role', 'admin')->first()?->id;
            if ($adminId) {
                DB::table('notifications')->insert([
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\\Notifications\\UserPhoneUpdated',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $adminId,
                    'data' => json_encode([
                        'message' => "Pelanggan {$user->name} telah mengubah nomor HP dari {$user->phone} ke {$validated['phone']} dan menunggu verifikasi.",
                        'user_id' => $user->id,
                        'type' => 'phone_verification',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $user->update($validated);

        $message = $user->isPendingVerification()
            ? 'Profil diperbarui. Nomor HP baru Anda sedang menunggu verifikasi Admin.'
            : 'Profil berhasil diperbarui.';

        return back()->with('success', $message);
    }
}
