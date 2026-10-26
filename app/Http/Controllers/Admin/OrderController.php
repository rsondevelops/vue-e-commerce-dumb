<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * Transisi status yang diperbolehkan. Key = status saat ini,
     * value = daftar status tujuan yang valid dari sana.
     */
    private const ALLOWED_TRANSITIONS = [
        'pending' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    /**
     * Daftar semua pesanan (admin melihat SEMUA, bukan cuma milik sendiri).
     */
    public function index(Request $request): Response
    {
        $orders = Order::query()
            ->withCount('items')
            ->when($request->search, function ($query, $search) {
                $query->where('order_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Detail 1 pesanan untuk admin.
     */
    public function show(Order $order): Response
    {
        $order->load('items.product', 'user');

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
            'allowedNextStatuses' => self::ALLOWED_TRANSITIONS[$order->status] ?? [],
        ]);
    }

    /**
     * Update status pesanan (SELAIN pembatalan — itu ditangani method cancel()
     * terpisah karena punya efek samping pengembalian stok yang berbeda).
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:processing,shipped,completed'],
        ]);

        $allowed = self::ALLOWED_TRANSITIONS[$order->status] ?? [];

        if (! in_array($validated['status'], $allowed, true)) {
            return back()->with('error', "Status tidak bisa diubah dari \"{$order->status}\" ke \"{$validated['status']}\".");
        }

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * Batalkan pesanan + kembalikan stok produk terkait.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'cancelled_reason' => ['required', 'string', 'max:500'],
        ]);

        $allowed = self::ALLOWED_TRANSITIONS[$order->status] ?? [];

        if (! in_array('cancelled', $allowed, true)) {
            return back()->with('error', "Pesanan berstatus \"{$order->status}\" tidak bisa dibatalkan.");
        }

        DB::transaction(function () use ($order, $validated) {
            $order->load('items.product');

            foreach ($order->items as $item) {
                // product_id bisa NULL jika produk sudah di-force-delete (Chapter 11) —
                // dalam kasus itu, tidak ada lagi baris products untuk dikembalikan stoknya.
                if ($item->product) {
                    $item->product->increment('stock', $item->qty);
                }
            }

            $order->update([
                'status' => 'cancelled',
                'cancelled_reason' => $validated['cancelled_reason'],
            ]);
        });

        return back()->with('success', 'Pesanan berhasil dibatalkan, stok telah dikembalikan.');
    }
}