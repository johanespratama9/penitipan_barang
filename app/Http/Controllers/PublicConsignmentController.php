<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Consignment;
use App\Models\Consignor;
use App\Models\User;
use App\Services\ConsignmentService;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicConsignmentController extends Controller
{
    public function index()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('public.titip', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'consignor_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'product_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:1|max:100',
            'selling_price' => 'required|numeric|min:1000',
            'purchase_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:2048',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            // 1. Cari atau daftarkan penitip berdasarkan nomor telepon
            $consignor = Consignor::firstOrCreate(
                ['phone' => $validated['phone']],
                [
                    'name' => $validated['consignor_name'],
                    'email' => $validated['email'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'status' => 'active',
                ]
            );

            // 2. Upload foto barang jika diunggah
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            // 3. Buat dokumen consignment status submitted
            $consignmentService = app(ConsignmentService::class);
            $consignment = $consignmentService->createConsignment(
                $consignor,
                [
                    'status' => 'submitted',
                    'notes' => 'Pengajuan penitipan online melalui form publik /titip. Catatan: ' . ($validated['description'] ?? '-'),
                ],
                [
                    [
                        'product_name' => $validated['product_name'],
                        'category_id' => $validated['category_id'],
                        'quantity' => $validated['quantity'],
                        'purchase_price' => $validated['purchase_price'] ?? 0,
                        'selling_price' => $validated['selling_price'],
                        'commission_type' => 'percentage',
                        'commission_value' => 20, // default 20% komisi toko
                        'notes' => 'Foto: ' . ($imagePath ?? 'Tidak ada'),
                    ],
                ]
            );

            // 4. Kirim notifikasi database ke admin jika ada user admin
            try {
                $admins = User::role('admin')->get();
                foreach ($admins as $admin) {
                    Notification::make()
                        ->title('Pengajuan Penitipan Barang Baru!')
                        ->body("Penitip {$consignor->name} mengajukan barang: {$validated['product_name']} ({$consignment->code})")
                        ->info()
                        ->sendToDatabase($admin);
                }
            } catch (\Throwable $e) {
                // Ignore if database notification table isn't migrated yet
            }

            return redirect()->route('titip.index')->with('success', [
                'code' => $consignment->code,
                'name' => $validated['product_name'],
                'consignor' => $consignor->name,
            ]);
        });
    }
}

