<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Inertia\Inertia;

class PingController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Diagnostics/Index');
    }

    public function ping(Request $request)
    {
        $data = $request->validate([
            'host' => [
                'required',
                'string',
                'max:253',
                'regex:/\A[a-zA-Z0-9.-]+\z/',
            ],
        ]);

        $host = $data['host'];

        $result = Process::timeout(5)->run([
            'ping',
            '-c',
            '1',
            '--',
            $host,
        ]);

        return back()->with('ping_result', [
            'host' => $host,
            'success' => $result->successful(),
            'output' => $result->output(),
            'error' => $result->errorOutput(),
        ]);
    }
}