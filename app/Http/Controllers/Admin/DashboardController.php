<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => $this->getStats(),
            'salesChart' => $this->getSalesChartData(),
            'recentOrders' => Order::latest()->take(5)->get(),
            'lowStockProducts' => Product::active()
                ->where('stock', '>', 0)
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->take(5)
                ->get(),
        ]);
    }

    /**
     * Kumpulan angka ringkas untuk 4 stat card.
     */
    private function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            'totalOrders' => Order::count(),

            'revenueThisMonth' => Order::where('status', 'completed')
                ->where('created_at', '>=', $startOfMonth)
                ->sum('total_amount'),

            'lowStockCount' => Product::active()
                ->where('stock', '>', 0)
                ->where('stock', '<=', 5)
                ->count(),

            'cancelledOrders' => Order::where('status', 'cancelled')
                ->where('created_at', '>=', $startOfMonth)
                ->count(),
        ];
    }

    /**
     * Data penjualan 7 hari terakhir, dikelompokkan per tanggal.
     * Tanggal tanpa penjualan tetap muncul dengan nilai 0 (bukan "loncat"),
     * supaya grafik tidak menyesatkan.
     */
    private function getSalesChartData(): array
    {
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();

        $salesPerDay = Order::where('status', 'completed')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $key = $date->format('Y-m-d');

            $labels[] = $date->translatedFormat('d M');
            $data[] = (int) ($salesPerDay[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}