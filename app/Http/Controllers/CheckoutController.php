<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    /**
     * Tampilkan form checkout. Data keranjang dibaca Vue dari localStorage,
     * bukan dari sini — Controller hanya menyiapkan data awal customer.
     */
    public function index(): Response
    {
        $user = Auth::user();

        return Inertia::render('Public/Checkout/Index', [
            'defaultCustomer' => [
                'name' => $user->name,
                'phone' => $user->phone,
                'address' => $user->address,
            ],
        ]);
    }

    /**
     * Proses pembuatan order.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_address' => ['required', 'string'],
            'payment_method' => ['required', 'in:transfer,cod'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            $orderItemsData = [];

            // ================================================
            // VALIDASI ULANG STOK & HARGA — TIDAK PERCAYA DATA DARI FRONTEND
            // ================================================
            foreach ($validated['items'] as $item) {
                $product = Product::active()
                    ->where('id', $item['product_id'])
                    ->lockForUpdate() // kunci baris ini agar tidak ada request lain
                    ->first();        // yang mengurangi stok produk yang sama secara bersamaan

                if (! $product) {
                    throw ValidationException::withMessages([
                        'items' => 'Salah satu produk di keranjang Anda sudah tidak tersedia.',
                    ]);
                }

                if ($product->stock < $item['qty']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok \"{$product->name}\" tidak mencukupi. Sisa stok: {$product->stock}.",
                    ]);
                }

                $subtotal = $product->price * $item['qty'];
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'product' => $product,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                ];
            }

            // ================================================
            // BUAT ORDER
            // ================================================
            $order = Order::create([
                'order_code' => $this->generateOrderCode(),
                'user_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            // ================================================
            // BUAT ORDER ITEMS (SNAPSHOT) + KURANGI STOK
            // ================================================
            foreach ($orderItemsData as $data) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $data['product']->id,
                    'product_name' => $data['product']->name,
                    'product_price' => $data['product']->price,
                    'qty' => $data['qty'],
                    'subtotal' => $data['subtotal'],
                ]);

                $data['product']->decrement('stock', $data['qty']);
            }

            return $order;
        });

        return redirect()
            ->route('checkout.success', $order->order_code)
            ->with('success', 'Pesanan berhasil dibuat!');
    }

    /**
     * Halaman konfirmasi order sukses.
     */
    public function success(string $orderCode): Response
    {
        $order = Order::with('items')
            ->where('order_code', $orderCode)
            ->where('user_id', Auth::id()) // pastikan hanya pemilik order yang bisa lihat
            ->firstOrFail();

        return Inertia::render('Public/Checkout/Success', [
            'order' => $order,
        ]);
    }

    /**
     * Generate kode order unik & mudah dibaca, misal: ORD-20260915-0001
     */
    private function generateOrderCode(): string
    {
        $prefix = 'ORD-' . now()->format('Ymd') . '-';
        $todayCount = Order::where('order_code', 'like', $prefix . '%')->count();

        return $prefix . str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);
    }
}