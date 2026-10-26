<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password yang Anda masukkan salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('home'));
    }

    /**
     * Tampilkan form register.
     */
    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Proses register — akun baru SELALU berperan sebagai customer.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'customer',
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    /**
     * Logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}