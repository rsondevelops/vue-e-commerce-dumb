<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductCatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $selectedCategory = $request->kategori
            ? Category::where('slug', $request->kategori)->first()
            : null;

        $products = Product::query()
            ->active()
            ->with('category')

            // SEARCH PRODUK + SEARCH KATEGORI
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        });
                });
            })

            // FILTER KATEGORI
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                $query->where('category_id', $selectedCategory->id);
            })

            // SORTING
            ->when($request->sort, function ($query, $sort) {
                match ($sort) {
                    'termurah' => $query->orderBy('price', 'asc'),
                    'termahal' => $query->orderBy('price', 'desc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })

            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Public/Products/Index', [
            'products' => $products,

            'categories' => Category::active()
                ->withCount('products')
                ->orderBy('name')
                ->get(),

            'filters' => $request->only([
                'search',
                'kategori',
                'sort',
            ]),

            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function show(string $slug): Response
    {
        $product = Product::active()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->inStock()
            ->with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        return Inertia::render('Public/Products/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}