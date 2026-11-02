<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommandInjectionLabController extends Controller
{
    public function showForm(): Response
    {
        return Inertia::render('Lab/PingTool');
    }

    public function ping(Request $request)
    {
        $host = $request->input('host');

        $command = "ping -c 2 " . $host;

        $output = shell_exec($command . ' 2>&1');

        return back()->with('ping_result', [
            'command' => $command,
            'output' => $output,
        ]);
    }
}