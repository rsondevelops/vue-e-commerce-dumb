<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BannerController extends Controller
{
    /**
     * Tampilkan daftar banner, diurutkan sesuai sort_order.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Banners/Index', [
            'banners' => Banner::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Simpan banner baru — otomatis ditaruh di urutan PALING BAWAH.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $validated['image'] = $request->file('image')->store('banners', 'public');
        $validated['sort_order'] = (Banner::max('sort_order') ?? 0) + 1;

        Banner::create($validated);

        return back()->with('success', 'Banner berhasil ditambahkan.');
    }

    /**
     * Perbarui banner.
     */
    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($validated);

        return back()->with('success', 'Banner berhasil diperbarui.');
    }

    /**
     * Hapus banner.
     */
    public function destroy(Banner $banner): RedirectResponse
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return back()->with('success', 'Banner berhasil dihapus.');
    }

    /**
     * Naikkan urutan tampil banner (tukar sort_order dengan banner di atasnya).
     */
    public function moveUp(Banner $banner): RedirectResponse
    {
        $above = Banner::where('sort_order', '<', $banner->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if (! $above) {
            return back()->with('error', 'Banner ini sudah berada di urutan paling atas.');
        }

        $this->swapSortOrder($banner, $above);

        return back()->with('success', 'Urutan banner berhasil diperbarui.');
    }

    /**
     * Turunkan urutan tampil banner (tukar sort_order dengan banner di bawahnya).
     */
    public function moveDown(Banner $banner): RedirectResponse
    {
        $below = Banner::where('sort_order', '>', $banner->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if (! $below) {
            return back()->with('error', 'Banner ini sudah berada di urutan paling bawah.');
        }

        $this->swapSortOrder($banner, $below);

        return back()->with('success', 'Urutan banner berhasil diperbarui.');
    }

    private function swapSortOrder(Banner $a, Banner $b): void
    {
        $temp = $a->sort_order;
        $a->update(['sort_order' => $b->sort_order]);
        $b->update(['sort_order' => $temp]);
    }
}