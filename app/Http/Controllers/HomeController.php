<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index()
    {
        return redirect()->route('home');
    }
    public function indexi()
    {
        return view('welcome');
    }
    public function home(): Response
    {
        return Inertia::render('Home', [
            'banners' => Banner::activeOrdered()->get(),

            'categories' => Category::active()
                ->withCount('products')
                ->orderBy('name')
                ->get(),

            'featuredProducts' => Product::active()
                ->inStock()
                ->with('category')
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }
}