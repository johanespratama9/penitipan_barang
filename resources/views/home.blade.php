<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarinah Street – Portal Konsinyasi Terpercaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20 shadow-xs">
        <div class="max-w-5xl mx-auto px-4 py-3.5 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2.5 no-underline">
                <span class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-lg shadow-sm">S</span>
                <div>
                    <span class="text-base font-black tracking-tight text-slate-900">SARINAH STREET</span>
                    <span class="text-[10px] font-bold block uppercase tracking-wider text-amber-600 -mt-1">Portal Penitip</span>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('penitip.register') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-lg hover:bg-slate-100 transition">
                    Daftar Mitra
                </a>
                <a href="{{ route('titip.index') }}" class="text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 px-3.5 py-1.5 rounded-lg transition shadow-sm">
                    Titip Barang
                </a>
                <a href="/dasbor" class="text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-3 py-1.5 rounded-lg transition hidden sm:inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1">

        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white py-20 px-4">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 bg-amber-500/20 text-amber-400 text-xs font-bold px-3 py-1.5 rounded-full border border-amber-500/30 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Sistem Konsinyasi Digital Sarinah Street
                </div>
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight mb-5">
                    Titipkan Barangmu,<br>
                    <span class="text-amber-400">Kami Jualkan.</span>
                </h1>
                <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto leading-relaxed mb-8">
                    Platform konsinyasi modern untuk mitra penitip. Daftarkan barang Anda secara online, pantau status penjualan, dan terima hasil kapan saja.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('titip.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-sm rounded-xl shadow-lg transition">
                        Mulai Titip Barang
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="{{ route('penitip.register') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold text-sm rounded-xl border border-white/20 transition">
                        Daftar Mitra Penitip
                    </a>
                </div>
            </div>
        </section>

        <!-- Cara Kerja -->
        <section class="py-16 px-4 bg-white">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-10">
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900">Cara Kerja</h2>
                    <p class="text-slate-500 text-sm mt-1">Tiga langkah mudah untuk mulai menitipkan barang</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="text-xs font-black text-amber-600 uppercase tracking-wider mb-1">Langkah 1</div>
                        <h3 class="font-bold text-slate-900 text-sm mb-2">Daftar Akun Mitra</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Buat akun mitra penitip secara gratis. Verifikasi instan tanpa dokumen rumit.</p>
                    </div>

                    <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        </div>
                        <div class="text-xs font-black text-amber-600 uppercase tracking-wider mb-1">Langkah 2</div>
                        <h3 class="font-bold text-slate-900 text-sm mb-2">Daftarkan Barang</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Isi formulir pengajuan online dengan detail barang dan estimasi harga jual.</p>
                    </div>

                    <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                        <div class="text-xs font-black text-amber-600 uppercase tracking-wider mb-1">Langkah 3</div>
                        <h3 class="font-bold text-slate-900 text-sm mb-2">Terima Hasil Penjualan</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Pantau status real-time dan terima hasil bersih setelah komisi toko 20%.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Keuntungan -->
        <section class="py-16 px-4 bg-slate-50">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-10">
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900">Mengapa Sarinah Street?</h2>
                    <p class="text-slate-500 text-sm mt-1">Keuntungan menjadi mitra penitip kami</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-start gap-4 p-5 bg-white rounded-2xl border border-slate-200">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex-shrink-0 flex items-center justify-center mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 mb-1">Komisi Transparan 20%</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Tidak ada biaya tersembunyi. Anda terima 80% dari harga jual saat barang laku.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-5 bg-white rounded-2xl border border-slate-200">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex-shrink-0 flex items-center justify-center mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 mb-1">Pantau Status Real-Time</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Akses dashboard kapan saja untuk melihat status barang – menunggu, terjual, atau pending pencairan.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-5 bg-white rounded-2xl border border-slate-200">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex-shrink-0 flex items-center justify-center mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 mb-1">Pendaftaran Online Gratis</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Daftar dan ajukan penitipan dari mana saja tanpa perlu datang ke toko terlebih dahulu.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-5 bg-white rounded-2xl border border-slate-200">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex-shrink-0 flex items-center justify-center mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 mb-1">Etalase Fisik di Toko</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Barang Anda dipajang langsung di etalase toko Sarinah Street dan masuk sistem POS kasir.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Bottom -->
        <section class="py-16 px-4 bg-amber-500">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-xl sm:text-2xl font-black text-slate-950 mb-3">Siap Menjadi Mitra Penitip?</h2>
                <p class="text-amber-900/80 text-sm mb-7">Bergabunglah dengan ratusan mitra penitip yang sudah mempercayakan barangnya ke Sarinah Street.</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('penitip.register') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-sm rounded-xl shadow transition">
                        Daftar Sekarang – Gratis
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="{{ route('titip.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-white/30 hover:bg-white/50 text-slate-950 font-bold text-sm rounded-xl border border-amber-400 transition">
                        Sudah Punya Akun? Titip Langsung
                    </a>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6">
        <div class="max-w-5xl mx-auto px-4 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Sarinah Street &bull; Portal Penitip Konsinyasi Terpercaya
        </div>
    </footer>

</body>
</html>

