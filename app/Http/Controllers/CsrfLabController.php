<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CsrfLabController extends Controller
{
    public function showProfile(): Response
    {
        return Inertia::render('Lab/CsrfProfile', [
            'user' => Auth::user(),
        ]);
    }

    public function updateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = Auth::user();
        $user->email = $request->email;
        $user->save();

        return back()->with('status', 'Email berhasil diubah menjadi ' . $request->email);
    }
}