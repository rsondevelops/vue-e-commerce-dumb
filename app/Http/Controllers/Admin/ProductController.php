<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar produk dengan filter & search.
     */
    public function index(Request $request): Response
    {
        $products = Product::query()
            ->with('category')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('price', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['search', 'category_id', 'status']),
        ]);
    }

    /**
     * Tampilkan form tambah produk.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $validated['slug'] = $this->generateUniqueSlug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit produk.
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Perbarui produk.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        if ($validated['name'] !== $product->name) {
            $validated['slug'] = $this->generateUniqueSlug(
                $validated['name'],
                $product->id
            );
        }

        // Hapus image dari data update kalau tidak ada upload baru
        if ($request->hasFile('image')) {

            // Hapus gambar lama
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // Simpan gambar baru
            $validated['image'] = $request->file('image')->store(
                'products',
                'public'
            );

        } else {
            // Jangan update kolom image
            unset($validated['image']);
        }

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Soft delete produk (detail restorasi dibahas di Chapter 11).
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus (dapat dipulihkan di menu Sampah).');
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (
            Product::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
        /**
     * Tampilkan daftar produk yang sudah di-soft-delete (Sampah).
     */
    public function trashed(): Response
    {
        return Inertia::render('Admin/Products/Trashed', [
            'products' => Product::onlyTrashed()
                ->with('category')
                ->latest('deleted_at')
                ->paginate(10),
        ]);
    }

    /**
     * Pulihkan produk dari Sampah.
     */
    public function restore(int $id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return back()->with('success', "Produk \"{$product->name}\" berhasil dipulihkan.");
    }

    /**
     * Hapus produk secara permanen (tidak bisa dibatalkan).
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $productName = $product->name;
        $product->forceDelete();

        return back()->with('success', "Produk \"{$productName}\" berhasil dihapus permanen.");
    }
}