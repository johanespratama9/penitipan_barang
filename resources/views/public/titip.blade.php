<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengajuan Titip Barang – Sarinah Street</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Navbar Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20 shadow-xs">
        <div class="max-w-4xl mx-auto px-4 py-3.5 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2.5 text-decoration-none">
                <span class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-lg shadow-sm">S</span>
                <div>
                    <span class="text-base font-black tracking-tight text-slate-900">SARINAH STREET</span>
                    <span class="text-[10px] font-bold block uppercase tracking-wider text-amber-600 -mt-1">Portal Penitip</span>
                </div>
            </a>

            <!-- User Auth Bar -->
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-lg text-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-500">Mitra:</span>
                    <strong class="text-slate-800 max-w-[140px] truncate">{{ $user->name ?? 'Penitip' }}</strong>
                </div>

                <!-- Link to Admin/Penitip Dashboard -->
                <a href="/dasbor" class="text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span>Dashboard</span>
                </a>

                <!-- Logout Form -->
                <form action="{{ route('penitip.logout') }}" method="POST" class="inline m-0">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-rose-600 px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition" title="Keluar dari akun">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-8 w-full">

        <!-- Welcome Flash (After First Registration) -->
        @if(session('welcome_penitip'))
            <div class="mb-6 p-5 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-2xl shadow-md">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">👋</span>
                    <div>
                        <h3 class="font-extrabold text-base sm:text-lg">Selamat Datang, {{ session('welcome_penitip')['name'] }}!</h3>
                        <p class="text-xs sm:text-sm text-amber-50 mt-1">
                            Akun mitra penitip Anda telah berhasil dibuat dengan Kode: <strong class="underline font-mono">{{ session('welcome_penitip')['code'] }}</strong>.
                        </p>
                        <p class="text-xs text-amber-100 mt-1">
                            Silakan isi informasi barang di bawah ini untuk mengajukan penitipan pertama Anda ke Sarinah Street.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Success Alert (After Consignment Submitted) -->
        @if(session('success'))
            <div class="mb-6 p-5 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-900 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">🎉</span>
                    <div>
                        <h3 class="font-bold text-lg text-emerald-800">Pengajuan Berhasil Terkirim!</h3>
                        <p class="text-sm text-emerald-700 mt-1">
                            Terima kasih, <strong>{{ session('success')['consignor'] }}</strong>. Barang Anda <strong>{{ session('success')['name'] }}</strong> telah terdaftar dengan nomor dokumen:
                        </p>
                        <div class="mt-2 inline-block font-mono font-bold text-base px-3 py-1 bg-white border border-emerald-300 rounded-lg text-emerald-800">
                            {{ session('success')['code'] }}
                        </div>
                        <p class="text-xs text-emerald-600 mt-2">
                            Silakan bawa barang Anda ke toko Sarinah Street untuk diverifikasi dan langsung dipajang di etalase/POS kasir.
                        </p>
                        <div class="mt-4 flex gap-3">
                            <a href="/dasbor/consignments" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 text-white font-bold text-xs rounded-lg hover:bg-emerald-700 transition">
                                Pantau di Dashboard &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight">Formulir Penitipan Barang</h1>
                    <p class="text-slate-400 text-xs sm:text-sm mt-1">Titipkan barang berkualitas Anda di Sarinah Street dengan skema bagi hasil transparan.</p>
                </div>
                <div class="text-xs bg-slate-800 border border-slate-700 px-3 py-1.5 rounded-lg text-slate-300 flex-shrink-0">
                    Komisi Standar Toko: <strong class="text-amber-400">20%</strong>
                </div>
            </div>

            <form action="{{ route('titip.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <!-- Section 1: Data Penitip (Auto-Filled from Auth) -->
                <div>
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-4">
                        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span>1. Data Profil Penitip</span>
                            <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">Akun Terverifikasi</span>
                        </h2>
                        @if(!empty($consignor?->code))
                            <span class="text-xs font-mono font-bold text-slate-500">{{ $consignor->code }}</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                name="consignor_name"
                                value="{{ old('consignor_name', $consignor->name ?? $user->name) }}"
                                required
                                class="w-full px-3 py-2 bg-white border rounded-lg text-sm focus:ring-2 focus:ring-amber-500 @error('consignor_name') border-rose-500 @else border-slate-200 @enderror"
                            />
                            @error('consignor_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp / HP <span class="text-rose-500">*</span></label>
                            <input
                                type="tel"
                                name="phone"
                                value="{{ old('phone', $consignor->phone ?? '') }}"
                                required
                                placeholder="Contoh: 081234567890"
                                class="w-full px-3 py-2 bg-white border rounded-lg text-sm focus:ring-2 focus:ring-amber-500 @error('phone') border-rose-500 @else border-slate-200 @enderror"
                            />
                            @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $consignor->email ?? $user->email) }}"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Domisili</label>
                            <input
                                type="text"
                                name="address"
                                value="{{ old('address', $consignor->address ?? '') }}"
                                placeholder="Kota / Alamat singkat"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500"
                            />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Data Barang -->
                <div>
                    <h2 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                        <span>2. Informasi Barang yang Dititipkan</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Barang / Produk <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                name="product_name"
                                value="{{ old('product_name') }}"
                                required
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-amber-500 @error('product_name') border-rose-500 @else border-slate-200 @enderror"
                                placeholder="Contoh: Sepatu Sneakers Nike Air Jordan Size 42"
                            />
                            @error('product_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Barang <span class="text-rose-500">*</span></label>
                            <select name="category_id" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-amber-500 bg-white border-slate-200">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Unit (Qty) <span class="text-rose-500">*</span></label>
                            <input
                                type="number"
                                name="quantity"
                                value="{{ old('quantity', 1) }}"
                                min="1"
                                max="100"
                                required
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500"
                            />
                            @error('quantity') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Estimasi Harga Jual (Rp) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-xs font-bold text-slate-400">Rp</span>
                                <input
                                    type="number"
                                    name="selling_price"
                                    value="{{ old('selling_price') }}"
                                    min="1000"
                                    step="500"
                                    required
                                    placeholder="Contoh: 150000"
                                    class="w-full pl-9 pr-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-amber-500 font-semibold @error('selling_price') border-rose-500 @else border-slate-200 @enderror"
                                />
                            </div>
                            <span class="text-[11px] text-slate-400 mt-1 block">Harga banderol yang akan ditawarkan ke pembeli toko.</span>
                            @error('selling_price') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Harga Modal / Harapan Bersih (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-xs font-bold text-slate-400">Rp</span>
                                <input
                                    type="number"
                                    name="purchase_price"
                                    value="{{ old('purchase_price') }}"
                                    min="0"
                                    step="500"
                                    placeholder="Opsional (acuan pribadi)"
                                    class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500"
                                />
                            </div>
                            <span class="text-[11px] text-slate-400 mt-1 block">Sebagai patokan nilai barang bagi Anda.</span>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Foto Barang</label>
                            <input
                                type="file"
                                name="image"
                                accept="image/*"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-500 file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100"
                            />
                            <span class="text-[11px] text-slate-400 mt-1 block">Format JPG, PNG, atau WEBP max 2MB.</span>
                            @error('image') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi / Kondisi Barang</label>
                            <textarea
                                name="description"
                                rows="3"
                                placeholder="Jelaskan kondisi barang (baru/bekas, kelengkapan box, cacat minor jika ada, dsb)"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500"
                            >{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Ketentuan Titip Jual -->
                <div class="bg-amber-50/70 border border-amber-200 rounded-xl p-4 text-xs text-amber-900 space-y-1.5">
                    <div class="font-bold flex items-center gap-1 text-amber-800">
                        <span>ℹ️ Ketentuan Singkat Penitipan di Sarinah Street:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-amber-800/90 pl-1">
                        <li>Barang wajib dalam kondisi layak pakai dan tidak melanggar hukum.</li>
                        <li>Komisi toko standar adalah <strong>20%</strong> dari harga jual saat barang berhasil laku.</li>
                        <li>Setelah pengajuan online dikirim, Anda dapat langsung membawa barang ke toko fisik Sarinah Street.</li>
                        <li>Anda dapat memantau status laku &amp; saldo pencairan kapan saja melalui akun login Anda.</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-3.5 px-6 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-sm rounded-xl shadow-sm transition flex items-center justify-center gap-2"
                    >
                        <span>Kirim Pengajuan Titip Barang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12">
        <div class="max-w-4xl mx-auto px-4 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Sarinah Street &bull; Portal Penitip Konsinyasi Terpercaya
        </div>
    </footer>

</body>
</html>
