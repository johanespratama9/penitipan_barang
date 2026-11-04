<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $tableNumber ? 'Menu Meja ' . $tableNumber . ' – ' : 'Menu Digital – ' }}Sarinah Street</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        /* Hide scrollbar for category pills on mobile */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col pb-12 antialiased selection:bg-amber-100 selection:text-amber-900">

    <!-- Top Sticky Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 shadow-xs transition-all">
        <div class="max-w-4xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between gap-3">
                <!-- Brand & Logo -->
                <a href="{{ route('menu.digital', array_filter(['meja' => $tableNumber])) }}" class="flex items-center gap-2.5 no-underline">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-600 to-amber-500 text-white flex items-center justify-center font-black text-lg shadow-sm">
                        S
                    </span>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h1 class="text-base font-black tracking-tight text-slate-900 leading-none">SARINAH STREET</h1>
                        </div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600 leading-tight mt-0.5">Buku Menu Digital</p>
                    </div>
                </a>

                <!-- Table Badge (if scanned with table number) -->
                @if(!empty($tableNumber))
                    <div class="flex items-center gap-1.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white px-3 py-1.5 rounded-full shadow-sm">
                        <svg class="w-3.5 h-3.5 text-amber-100 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-xs font-black tracking-wide uppercase">{{ $tableNumber }}</span>
                    </div>
                @else
                    <div class="flex items-center gap-1 bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full text-[11px] font-medium border border-slate-200">
                        <span>Katalog Menu</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sticky Search & Filter Bar -->
        <div class="max-w-4xl mx-auto px-4 pb-3 pt-1">
            <!-- Search Input -->
            <div class="relative">
                <input 
                    type="text" 
                    id="menuSearchInput" 
                    placeholder="Cari menu, kopi, makanan, cemilan..." 
                    value="{{ $search }}"
                    class="w-full bg-slate-100 border border-slate-200 rounded-xl pl-9 pr-9 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition"
                    autocomplete="off"
                >
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <button 
                    id="clearSearchBtn" 
                    type="button" 
                    class="hidden absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 p-0.5"
                    onclick="clearSearch()"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Categories Horizontal Pill List -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pt-2.5 pb-0.5">
                <button 
                    type="button" 
                    data-category="all"
                    class="category-filter-btn shrink-0 text-xs font-semibold px-3 py-1.5 rounded-full transition {{ empty($categorySlug) || $categorySlug === 'all' ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    onclick="filterCategory('all', this)"
                >
                    Semua ({{ $totalAvailableProducts }})
                </button>
                @foreach($categories as $cat)
                    <button 
                        type="button" 
                        data-category="{{ $cat->slug }}"
                        class="category-filter-btn shrink-0 text-xs font-semibold px-3 py-1.5 rounded-full transition {{ $categorySlug === $cat->slug ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                        onclick="filterCategory('{{ $cat->slug }}', this)"
                    >
                        {{ $cat->name }}
                        @if($cat->products_count > 0)
                            <span class="opacity-75 text-[10px] ml-0.5">({{ $cat->products_count }})</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-4xl mx-auto px-4 py-4 w-full flex-1">
        
        <!-- Notice Banner: Tampilan Saja (Tidak untuk pesan online) -->
        <div class="mb-4 bg-amber-50/90 border border-amber-200/80 rounded-2xl p-3.5 shadow-xs flex items-start gap-3 text-amber-900">
            <div class="w-8 h-8 rounded-xl bg-amber-500/15 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-xs sm:text-sm leading-relaxed">
                <p class="font-bold text-amber-950">
                    Katalog Menu Digital
                    @if(!empty($tableNumber))
                        – Meja {{ $tableNumber }}
                    @endif
                </p>
                <p class="text-amber-800/90 text-xs mt-0.5">
                    Halaman ini hanya untuk menampilkan menu & daftar harga produk. <strong>Untuk pemesanan</strong>, silakan sampaikan langsung pesanan Anda ke pelayan atau kasir kami.
                </p>
            </div>
        </div>

        <!-- Product Count Indicator & Status -->
        <div class="flex items-center justify-between text-xs text-slate-500 mb-3 px-1">
            <span id="productCountLabel">Menampilkan {{ $products->count() }} menu</span>
            <span class="text-[11px] text-emerald-600 font-medium flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-ping"></span>
                Daftar Menu Realtime
            </span>
        </div>

        <!-- Product Grid -->
        @if($products->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 p-8 shadow-xs">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum ada menu yang tersedia</h3>
                <p class="text-xs text-slate-500 max-w-xs mx-auto mt-1">Silakan pilih kategori lain atau periksa kembali kata kunci pencarian Anda.</p>
                <button 
                    onclick="resetAllFilters()" 
                    class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 text-white text-xs font-semibold rounded-xl hover:bg-amber-600 transition"
                >
                    Lihat Semua Menu
                </button>
            </div>
        @else
            <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                @foreach($products as $product)
                    @php
                        $hasImage = !empty($product->image);
                        $imageUrl = $hasImage ? asset('storage/' . $product->image) : null;
                        $formattedPrice = 'Rp ' . number_format((float) $product->selling_price, 0, ',', '.');
                        $catSlug = $product->category ? $product->category->slug : 'tanpa-kategori';
                        $catName = $product->category ? $product->category->name : 'Umum';
                    @endphp

                    <div 
                        class="product-card bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 flex flex-col cursor-pointer group"
                        data-id="{{ $product->id }}"
                        data-name="{{ strtolower($product->name) }}"
                        data-desc="{{ strtolower($product->description ?? '') }}"
                        data-category="{{ $catSlug }}"
                        data-code="{{ strtolower($product->code) }}"
                        onclick="openProductDetail({{ json_encode([
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $formattedPrice,
                            'category' => $catName,
                            'code' => $product->code,
                            'stock' => $product->stock,
                            'image' => $imageUrl,
                            'description' => $product->description ?: 'Menu lezat siap disajikan.',
                        ]) }})"
                    >
                        <!-- Image Container -->
                        <div class="relative w-full aspect-square bg-slate-100 overflow-hidden">
                            @if($hasImage)
                                <img 
                                    src="{{ $imageUrl }}" 
                                    alt="{{ $product->name }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    loading="lazy"
                                >
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 p-4 bg-gradient-to-br from-slate-50 to-slate-100">
                                    <svg class="w-10 h-10 mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-[10px] text-slate-400 font-medium">Foto Menu</span>
                                </div>
                            @endif

                            <!-- Category Badge -->
                            <div class="absolute top-2 left-2">
                                <span class="bg-black/60 backdrop-blur-xs text-white text-[10px] font-semibold px-2 py-0.5 rounded-md shadow-xs">
                                    {{ $catName }}
                                </span>
                            </div>

                            <!-- Stock Badge -->
                            <div class="absolute top-2 right-2">
                                @if($product->stock > 5)
                                    <span class="bg-emerald-500/90 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-xs">
                                        Tersedia
                                    </span>
                                @elseif($product->stock > 0)
                                    <span class="bg-amber-500/90 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-xs">
                                        Sisa {{ $product->stock }}
                                    </span>
                                @else
                                    <span class="bg-rose-500/90 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-xs">
                                        Habis
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-3 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-xs sm:text-sm text-slate-900 line-clamp-2 group-hover:text-amber-600 transition-colors leading-snug">
                                    {{ $product->name }}
                                </h3>

                                @if(!empty($product->description))
                                    <p class="text-[11px] text-slate-500 line-clamp-1 mt-1 leading-normal">
                                        {{ $product->description }}
                                    </p>
                                @endif
                            </div>

                            <!-- Price Row -->
                            <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs sm:text-sm font-black text-amber-600">
                                    {{ $formattedPrice }}
                                </span>
                                <span class="text-[10px] text-slate-400 group-hover:text-amber-500 flex items-center gap-0.5 font-medium transition">
                                    Detail
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- No Results from Search Indicator (Hidden by default) -->
            <div id="noResultsAlert" class="hidden text-center py-12 bg-white rounded-2xl border border-slate-200 p-8 shadow-xs mt-4">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 mx-auto flex items-center justify-center mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Menu tidak ditemukan</h4>
                <p class="text-xs text-slate-500 mt-0.5">Coba gunakan kata kunci pencarian lain.</p>
                <button 
                    onclick="clearSearch()" 
                    class="mt-3 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition"
                >
                    Reset Pencarian
                </button>
            </div>
        @endif

    </main>

    <!-- Floating Info Footer -->
    <div class="fixed bottom-0 inset-x-0 z-20 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-4 py-2.5 shadow-lg">
        <div class="max-w-4xl mx-auto flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-slate-600">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>
                    @if(!empty($tableNumber))
                        Anda berada di <strong>Meja {{ $tableNumber }}</strong>
                    @else
                        Silakan pilih menu favorit Anda
                    @endif
                </span>
            </div>
            <div class="text-[11px] font-bold text-amber-700 bg-amber-100/80 px-2.5 py-1 rounded-lg">
                Pesan Langsung ke Kasir / Waiter
            </div>
        </div>
    </div>

    <!-- Product Detail Modal (Pop-up saat klik produk) -->
    <div 
        id="detailModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden transition-opacity"
        onclick="closeProductDetail(event)"
    >
        <div 
            class="bg-white rounded-3xl max-w-sm sm:max-w-md w-full overflow-hidden shadow-2xl transform transition-all flex flex-col max-h-[90vh]"
            onclick="event.stopPropagation()"
        >
            <!-- Modal Header / Image -->
            <div class="relative w-full aspect-video bg-slate-100 overflow-hidden shrink-0">
                <img 
                    id="modalImage" 
                    src="" 
                    alt="Foto Menu" 
                    class="w-full h-full object-cover hidden"
                >
                <div id="modalImagePlaceholder" class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-amber-50 to-slate-100 text-slate-300">
                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-xs text-slate-400 font-medium mt-1">Sarinah Street</span>
                </div>

                <!-- Close Button -->
                <button 
                    onclick="closeProductDetail()" 
                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Category Pill -->
                <div class="absolute bottom-3 left-3">
                    <span id="modalCategory" class="bg-black/60 backdrop-blur-xs text-white text-[11px] font-semibold px-2.5 py-1 rounded-md">
                        Kategori
                    </span>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-5 overflow-y-auto flex-1">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 id="modalName" class="text-lg font-black text-slate-900 leading-tight">Nama Menu</h2>
                        <p id="modalCode" class="text-[11px] text-slate-400 font-mono mt-0.5">Kode</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span id="modalPrice" class="text-lg font-black text-amber-600 block leading-tight">Rp 0</span>
                        <span id="modalStock" class="text-[11px] text-emerald-600 font-bold block mt-0.5">Tersedia</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Deskripsi Menu:</h4>
                    <p id="modalDescription" class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed whitespace-pre-line">
                        Deskripsi menu akan ditampilkan di sini.
                    </p>
                </div>

                <!-- Petunjuk Pemesanan -->
                <div class="mt-5 bg-amber-50 rounded-2xl p-3 border border-amber-200/80 text-amber-900 flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs leading-snug">
                        Untuk memesan <strong>menu ini</strong>, catat nama menu lalu sampaikan ke pelayan / kasir di meja pemesanan.
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                <button 
                    onclick="closeProductDetail()" 
                    class="w-full sm:w-auto px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Client-side Search and Filter JavaScript -->
    <script>
        let currentCategory = '{{ $categorySlug ?: "all" }}';

        const searchInput = document.getElementById('menuSearchInput');
        const clearBtn = document.getElementById('clearSearchBtn');
        const productGrid = document.getElementById('productGrid');
        const productCountLabel = document.getElementById('productCountLabel');
        const noResultsAlert = document.getElementById('noResultsAlert');
        const cards = document.querySelectorAll('.product-card');

        // Search event listener
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                if (query.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
                filterProducts();
            });
        }

        function clearSearch() {
            if (searchInput) {
                searchInput.value = '';
                clearBtn.classList.add('hidden');
                filterProducts();
            }
        }

        function filterCategory(catSlug, btnEl) {
            currentCategory = catSlug;

            // Update UI buttons
            document.querySelectorAll('.category-filter-btn').forEach(btn => {
                btn.classList.remove('bg-amber-500', 'text-white', 'shadow-xs');
                btn.classList.add('bg-slate-100', 'text-slate-600');
            });

            if (btnEl) {
                btnEl.classList.remove('bg-slate-100', 'text-slate-600');
                btnEl.classList.add('bg-amber-500', 'text-white', 'shadow-xs');
            }

            filterProducts();
        }

        function filterProducts() {
            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const desc = card.getAttribute('data-desc') || '';
                const code = card.getAttribute('data-code') || '';
                const category = card.getAttribute('data-category') || '';

                const matchesCategory = (currentCategory === 'all' || category === currentCategory);
                const matchesSearch = query === '' || name.includes(query) || desc.includes(query) || code.includes(query);

                if (matchesCategory && matchesSearch) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            if (productCountLabel) {
                productCountLabel.textContent = `Menampilkan ${visibleCount} menu`;
            }

            if (noResultsAlert) {
                if (visibleCount === 0 && cards.length > 0) {
                    noResultsAlert.classList.remove('hidden');
                } else {
                    noResultsAlert.classList.add('hidden');
                }
            }
        }

        function resetAllFilters() {
            clearSearch();
            const allBtn = document.querySelector('[data-category="all"]');
            if (allBtn) {
                filterCategory('all', allBtn);
            }
        }

        // Modal functions
        const detailModal = document.getElementById('detailModal');
        const modalImage = document.getElementById('modalImage');
        const modalImagePlaceholder = document.getElementById('modalImagePlaceholder');
        const modalCategory = document.getElementById('modalCategory');
        const modalName = document.getElementById('modalName');
        const modalCode = document.getElementById('modalCode');
        const modalPrice = document.getElementById('modalPrice');
        const modalStock = document.getElementById('modalStock');
        const modalDescription = document.getElementById('modalDescription');

        function openProductDetail(product) {
            modalName.textContent = product.name;
            modalCode.textContent = 'Kode: ' + product.code;
            modalPrice.textContent = product.price;
            modalCategory.textContent = product.category;
            modalDescription.textContent = product.description;

            if (product.stock > 0) {
                modalStock.textContent = 'Tersedia (' + product.stock + ' porsi/unit)';
                modalStock.className = 'text-[11px] text-emerald-600 font-bold block mt-0.5';
            } else {
                modalStock.textContent = 'Stok Habis';
                modalStock.className = 'text-[11px] text-rose-600 font-bold block mt-0.5';
            }

            if (product.image) {
                modalImage.src = product.image;
                modalImage.classList.remove('hidden');
                modalImagePlaceholder.classList.add('hidden');
            } else {
                modalImage.src = '';
                modalImage.classList.add('hidden');
                modalImagePlaceholder.classList.remove('hidden');
            }

            detailModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeProductDetail(e) {
            detailModal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Escape key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !detailModal.classList.contains('hidden')) {
                closeProductDetail();
            }
        });
    </script>
</body>
</html>

