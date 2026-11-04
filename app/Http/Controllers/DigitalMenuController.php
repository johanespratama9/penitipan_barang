<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class DigitalMenuController extends Controller
{
    /**
     * Tampilkan halaman menu digital publik (hanya untuk melihat menu/katalog, bukan untuk order).
     */
    public function index(Request $request)
    {
        $tableNumber = trim((string) $request->query('meja', ''));
        // Batasi panjang nomor meja untuk keamanan display
        if (mb_strlen($tableNumber) > 40) {
            $tableNumber = mb_substr($tableNumber, 0, 40);
        }

        $categorySlug = $request->query('kategori');
        $search = trim((string) $request->query('q', ''));

        // Ambil semua kategori aktif yang memiliki produk tersedia (atau semua kategori aktif)
        $categories = Category::active()
            ->withCount(['products' => function ($query) {
                $query->where('status', 'available');
            }])
            ->orderBy('name')
            ->get();

        // Query produk yang statusnya 'available'
        $productsQuery = Product::with('category')
            ->where('status', 'available');

        if (! empty($categorySlug) && $categorySlug !== 'all') {
            $productsQuery->whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug)
                    ->orWhere('id', $categorySlug);
            });
        }

        if (! empty($search)) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $products = $productsQuery->orderBy('name')->get();

        // Total produk tersedia keseluruhan untuk counter
        $totalAvailableProducts = Product::where('status', 'available')->count();

        return view('public.menu', compact(
            'products',
            'categories',
            'tableNumber',
            'categorySlug',
            'search',
            'totalAvailableProducts'
        ));
    }
}

