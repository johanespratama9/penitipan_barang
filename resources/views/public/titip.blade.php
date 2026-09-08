<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Titip Barang - Sistem Penitipan Konsinyasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">
    <!-- Navbar Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-xl font-bold text-blue-600">📦 TitipBarang</span>
                <span class="text-xs bg-blue-100 text-blue-800 font-semibold px-2 py-0.5 rounded">Consignment</span>
            </div>
            <a href="/admin/login" class="text-sm text-gray-600 hover:text-blue-600 font-medium">
                Login Panel &rarr;
            </a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-8">
        <!-- Success Alert -->
        @if(session('success'))
            <div class="mb-6 p-5 bg-green-50 border border-green-200 rounded-xl text-green-900 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">🎉</span>
                    <div>
                        <h3 class="font-bold text-lg text-green-800">Pengajuan Berhasil Dikirim!</h3>
                        <p class="text-sm text-green-700 mt-1">
                            Terima kasih, <strong>{{ session('success')['consignor'] }}</strong>. Barang Anda <strong>{{ session('success')['name'] }}</strong> telah terdaftar dengan nomor dokumen:
                        </p>
                        <div class="mt-2 inline-block font-mono font-bold text-base px-3 py-1 bg-white border border-green-300 rounded-lg text-green-800">
                            {{ session('success')['code'] }}
                        </div>
                        <p class="text-xs text-green-600 mt-2">
                            Silakan bawa barang Anda ke toko kami untuk diverifikasi oleh admin toko, atau hubungi customer service kami.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                <h1 class="text-2xl font-bold">Formulir Penitipan Barang</h1>
                <p class="text-blue-100 text-sm mt-1">Punya barang berkualitas yang ingin dijualkan? Titipkan di toko kami dengan sistem komisi yang transparan.</p>
            </div>

            <form action="{{ route('titip.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <!-- Section 1: Data Penitip -->
                <div>
                    <h2 class="text-base font-bold text-gray-900 border-b pb-2 mb-4 flex items-center gap-2">
                        <span>1. Data Diri Penitip</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="consignor_name" value="{{ old('consignor_name') }}" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('consignor_name') border-red-500 @enderror" placeholder="Contoh: Ahmad Dahlan" />
                            @error('consignor_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor WhatsApp / HP <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror" placeholder="Contoh: 081234567890" />
                            @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email (Opsional)</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="nama@email.com" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Domisili (Opsional)</label>
                            <input type="text" name="address" value="{{ old('address') }}" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Kota / Alamat singkat" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Data Barang -->
                <div>
                    <h2 class="text-base font-bold text-gray-900 border-b pb-2 mb-4 flex items-center gap-2">
                        <span>2. Informasi Barang yang Dititipkan</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Barang / Produk <span class="text-red-500">*</span></label>
                            <input type="text" name="product_name" value="{{ old('product_name') }}" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('product_name') border-red-500 @enderror" placeholder="Contoh: Sepatu Sneakers Nike Air Jordan Size 42" />
                            @error('product_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori Barang <span class="text-red-500">*</span></label>
                            <select name="category_id" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah Unit (Qty) <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="100" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Estimasi Harga Jual (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="selling_price" value="{{ old('selling_price') }}" required min="1000" step="1000" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('selling_price') border-red-500 @enderror" placeholder="Contoh: 250000" />
                            <p class="text-xs text-gray-500 mt-1">Komisi standar toko 20% akan dipotong saat barang laku terjual.</p>
                            @error('selling_price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Minimum / Acuan Anda (Rp)</label>
                            <input type="number" name="purchase_price" value="{{ old('purchase_price') }}" min="0" step="1000" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Opsional" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Foto Barang (Opsional)</label>
                            <input type="file" name="image" accept="image/*" class="w-full px-3 py-1.5 border rounded-lg text-xs file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <p class="text-xs text-gray-400 mt-0.5">Format JPG/PNG max 2MB.</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan / Kondisi Barang</label>
                            <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Ceritakan kelengkapan, minus, atau kondisi barang secara jujur...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm shadow-md transition flex items-center gap-2">
                        Kirim Pengajuan Titip Barang &rarr;
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

