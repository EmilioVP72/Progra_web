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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastname1' => ['required', 'string', 'max:255'],
            'lastname2' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15', 'unique:'.User::class],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], 
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $photoPath = null;
        
        if($request->hasFile('photo')){
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $user = User::create([
            'name' => $validatedData['name'],
            'lastname1' => $validatedData['lastname1'],
            'lastname2' => $validatedData['lastname2'] ?? null,
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'photo' => $photoPath
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
