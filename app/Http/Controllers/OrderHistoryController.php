<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OrderHistoryController extends Controller
{
    /**
     * Daftar pesanan milik customer yang sedang login.
     */
    public function index(Request $request): Response
    {
        $orders = Order::where('user_id', Auth::id())
            ->withCount('items')
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return Inertia::render('Public/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['status']),
        ]);
    }

    /**
     * Detail 1 pesanan — WAJIB pastikan milik user yang login.
     */
    public function show(string $orderCode): Response
    {
        $order = Order::with('items.product')
            ->where('order_code', $orderCode)
            ->where('user_id', Auth::id()) // ← proteksi ownership, WAJIB ada
            ->firstOrFail();

        return Inertia::render('Public/Orders/Show', [
            'order' => $order,
        ]);
    }
}