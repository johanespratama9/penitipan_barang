<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Mitra Penitip – Sarinah Street</title>
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
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-5xl mx-auto px-4 py-3.5 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2.5 text-decoration-none">
                <span class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-lg shadow-sm">S</span>
                <div>
                    <span class="text-base font-black tracking-tight text-slate-900">SARINAH STREET</span>
                    <span class="text-[10px] font-bold block uppercase tracking-wider text-amber-600 -mt-1">Mitra Konsinyasi</span>
                </div>
            </a>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 hidden sm:inline">Sudah punya akun?</span>
                <a href="/dasbor/login" class="text-xs sm:text-sm font-semibold text-slate-700 hover:text-amber-600 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition">
                    Masuk / Login &rarr;
                </a>
            </div>
        </div>
    </header>

    <!-- Main Registration Section -->
    <main class="max-w-4xl mx-auto px-4 py-8 sm:py-12 w-full">

        @if(session('info'))
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-sm flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left Column: Value Proposition & Benefit -->
            <div class="lg:col-span-5 space-y-6">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wider mb-3">
                        Pendaftaran Penitip
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Titipkan Barang Anda, Kami yang Jualkan.
                    </h1>
                    <p class="text-sm text-slate-600 mt-2.5 leading-relaxed">
                        Sebelum mengajukan barang titipan di <strong class="text-slate-800">Sarinah Street</strong>, silakan buat akun Anda terlebih dahulu agar Anda dapat memantau status penjualan dan saldo secara real-time.
                    </p>
                </div>

                <!-- Feature checklist -->
                <div class="space-y-3.5 pt-2">
                    <div class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-slate-200 shadow-sm">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Dashboard Pribadi Real-Time</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Cek stok barang terjual dan komisi langsung dari ponsel kapan saja.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-slate-200 shadow-sm">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Pencairan Dana Otomatis</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Hak penjualan ditransfer secara transparan dengan rincian bukti bank.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-slate-200 shadow-sm">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Akun Terproteksi Aman</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Data pribadi dan transaksi hanya bisa diakses oleh Anda sendiri.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Registration Form -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 bg-slate-900 text-white">
                        <h2 class="text-lg font-bold">Formulir Pendaftaran Mitra</h2>
                        <p class="text-xs text-slate-400 mt-1">Lengkapi data berikut untuk membuat akun login dan profil penitip Anda.</p>
                    </div>

                    <form action="{{ route('penitip.register.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                placeholder="Contoh: Budi Santoso"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-amber-500 transition @error('name') border-rose-500 @else border-slate-200 @enderror"
                            />
                            @error('name') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Grid WhatsApp & Email -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    No. WhatsApp / HP <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="tel"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    required
                                    placeholder="Contoh: 081234567890"
                                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-amber-500 transition @error('phone') border-rose-500 @else border-slate-200 @enderror"
                                />
                                @error('phone') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Alamat Email (untuk Login) <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="nama@email.com"
                                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-amber-500 transition @error('email') border-rose-500 @else border-slate-200 @enderror"
                                />
                                @error('email') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- No. KTP / NIK (Opsional) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                No. Identitas KTP / NIK <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input
                                type="text"
                                name="identity_number"
                                value="{{ old('identity_number') }}"
                                placeholder="16 digit NIK KTP"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-amber-500 transition"
                            />
                        </div>

                        <!-- Alamat Domisili -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Alamat Domisili <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea
                                name="address"
                                rows="2"
                                placeholder="Kota / Alamat singkat tempat tinggal"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-amber-500 transition"
                            >{{ old('address') }}</textarea>
                        </div>

                        <!-- Grid Password & Konfirmasi -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Password Login <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="password"
                                    name="password"
                                    required
                                    placeholder="Minimal 6 karakter"
                                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-amber-500 transition @error('password') border-rose-500 @else border-slate-200 @enderror"
                                />
                                @error('password') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Konfirmasi Password <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    placeholder="Ulangi password"
                                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-amber-500 transition"
                                />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3">
                            <button
                                type="submit"
                                class="w-full py-3 px-4 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-sm rounded-xl shadow-sm transition flex items-center justify-center gap-2"
                            >
                                <span>Daftar Akun &amp; Mulai Titip Barang</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </button>
                        </div>

                        <div class="text-center pt-2">
                            <p class="text-xs text-slate-500">
                                Sudah memiliki akun mitra?
                                <a href="/dasbor/login" class="font-bold text-amber-600 hover:underline">Masuk ke akun Anda</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12">
        <div class="max-w-5xl mx-auto px-4 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Sarinah Street &bull; Sistem Titip Jual &amp; Manajemen Konsinyasi Terpercaya
        </div>
    </footer>

</body>
</html>

