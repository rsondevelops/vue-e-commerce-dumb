<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class XssLabController extends Controller
{
    public function index(): Response
    {
        $comments = DB::table('lab_comments')->latest()->get();

        return Inertia::render('Lab/XssComment', [
            'comments' => $comments,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'message' => 'required|string',
        ]);

        DB::table('lab_comments')->insert([
            'name' => $request->name,
            'message' => $request->message, // RENTAN: tidak di-sanitize
            'created_at' => now(),
        ]);

        return back();
    }
}