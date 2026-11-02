<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SqlInjectionLabController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Auth/LoginInjection');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";

        $results = DB::select($sql);

        if (count($results) > 0) {
            $user = User::find($results[0]->id);
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('home')->with('login_result', [
                'success' => true,
                'message' => 'Login berhasil',
                'query' => $sql,
            ]);
        }

        return back()->with('login_result', [
            'success' => false,
            'message' => 'Email atau password salah',
            'query' => $sql,
        ]);
    }
}