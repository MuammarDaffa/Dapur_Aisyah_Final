<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|08)[0-9]{8,13}$/', 'unique:'.User::class],
            'password' => ['required', Rules\Password::min(8)],
        ]);

        // Auto-generate a unique email placeholder from phone number
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
        $email = $cleanPhone . '@dapur-aisyah.local';

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('customer.dashboard'));
    }
}
