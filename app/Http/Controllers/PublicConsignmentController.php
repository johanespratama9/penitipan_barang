<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Consignment;
use App\Models\Consignor;
use App\Models\User;
use App\Services\ConsignmentService;
use App\Services\ImageService;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PublicConsignmentController extends Controller
{
    /**
     * Tampilkan formulir penitipan barang bagi mitra yang sudah login.
     */
    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('penitip.register')->with('info', 'Silakan buat akun atau login terlebih dahulu untuk mengakses formulir penitipan barang.');
        }

        $user = Auth::user();

        // Cari atau hubungkan data profil penitip milik user ini
        $consignor = Consignor::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        if ($consignor && ! $consignor->user_id) {
            $consignor->update(['user_id' => $user->id]);
        }

        $categories = Category::active()->orderBy('name')->get();

        return view('public.titip', compact('categories', 'consignor', 'user'));
    }

    /**
     * Simpan pengajuan penitipan barang dari mitra.
     */
    public function store(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('penitip.register')->with('info', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'consignor_name'  => 'required|string|max:255',
            'phone'           => 'required|string|max:50',
            'email'           => 'nullable|email|max:255',
            'address'         => 'nullable|string|max:500',
            'product_name'    => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'quantity'        => 'required|integer|min:1|max:100',
            'selling_price'   => 'required|numeric|min:1000',
            'purchase_price'  => 'nullable|numeric|min:0',
            'description'     => 'nullable|string|max:1000',
            'image'           => 'nullable|image|max:2048',
        ], [
            'product_name.required'  => 'Nama barang wajib diisi.',
            'category_id.required'   => 'Pilih kategori barang yang sesuai.',
            'quantity.required'      => 'Jumlah unit barang wajib diisi.',
            'selling_price.required' => 'Estimasi harga jual wajib diisi.',
        ]);

        return DB::transaction(function () use ($request, $validated, $user) {
            // 1. Dapatkan atau perbarui profil penitip milik user yang login
            $consignor = Consignor::where('user_id', $user->id)->first();

            if (! $consignor) {
                $consignor = Consignor::where('phone', $validated['phone'])->first();
            }

            if ($consignor) {
                $consignor->update([
                    'user_id' => $user->id,
                    'name'    => $validated['consignor_name'],
                    'phone'   => $validated['phone'],
                    'email'   => $validated['email'] ?? $consignor->email,
                    'address' => $validated['address'] ?? $consignor->address,
                    'status'  => 'active',
                ]);
            } else {
                $consignor = Consignor::create([
                    'user_id' => $user->id,
                    'name'    => $validated['consignor_name'],
                    'phone'   => $validated['phone'],
                    'email'   => $validated['email'] ?? $user->email,
                    'address' => $validated['address'] ?? null,
                    'status'  => 'active',
                ]);
            }

            // 2. Upload foto barang jika diunggah (dikonversi ke WebP otomatis)
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = app(ImageService::class)->storeAsWebp(
                    $request->file('image'),
                    directory: 'products',
                    quality: 82,
                );
            }

            // 3. Buat dokumen consignment status submitted
            $consignmentService = app(ConsignmentService::class);
            $consignment = $consignmentService->createConsignment(
                $consignor,
                [
                    'status' => 'submitted',
                    'notes'  => 'Pengajuan penitipan online oleh mitra: ' . $consignor->name . '. Catatan: ' . ($validated['description'] ?? '-'),
                ],
                [
                    [
                        'product_name'     => $validated['product_name'],
                        'category_id'      => $validated['category_id'],
                        'quantity'         => $validated['quantity'],
                        'purchase_price'   => $validated['purchase_price'] ?? 0,
                        'selling_price'    => $validated['selling_price'],
                        'commission_type'  => 'percentage',
                        'commission_value' => 20, // default 20% komisi toko
                        'notes'            => 'Foto: ' . ($imagePath ?? 'Tidak ada'),
                    ],
                ]
            );

            // 4. Kirim notifikasi database ke admin jika ada user admin
            try {
                $admins = User::role('admin')->get();
                foreach ($admins as $admin) {
                    Notification::make()
                        ->title('Pengajuan Penitipan Barang Baru!')
                        ->body("Mitra {$consignor->name} mengajukan barang: {$validated['product_name']} ({$consignment->code})")
                        ->info()
                        ->sendToDatabase($admin);
                }
            } catch (\Throwable $e) {
                // Ignore jika database notification table belum termigrasi
            }

            return redirect()->route('titip.index')->with('success', [
                'code'      => $consignment->code,
                'name'      => $validated['product_name'],
                'consignor' => $consignor->name,
            ]);
        });
    }
}
