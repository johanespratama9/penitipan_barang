<x-filament-panels::page>
    <!-- Include HTML5 QR/Barcode Scanner Script -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <div x-data="posApp()" class="space-y-4">
        <!-- Top Action Bar: Search, Kamera HP, & Mobile Tab Switcher -->
        <div class="bg-white dark:bg-gray-800 p-3 sm:p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-3">
            <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center justify-between">
                <!-- Barcode Scanner Input Form -->
                <form wire:submit="scanBarcode" class="flex-1 flex gap-2">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            wire:model="barcode"
                            placeholder="Ketik/Scan Barcode..."
                            class="w-full pl-10 pr-4 py-2.5 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-heroicon-o-qr-code class="w-5 h-5" />
                        </div>
                    </div>
                    <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl text-sm font-semibold transition flex items-center gap-1">
                        Enter
                    </button>
                </form>

                <!-- Tombol Scan Kamera HP & Search Barang -->
                <div class="flex items-center gap-2">
                    <!-- Tombol Buka Kamera Scanner HP -->
                    <button
                        type="button"
                        @click="openScanner()"
                        class="flex-1 sm:flex-none px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-sm transition flex items-center justify-center gap-2 active:scale-95"
                    >
                        <x-heroicon-o-camera class="w-5 h-5" />
                        <span>Scan Kamera HP</span>
                    </button>

                    <!-- Search Input -->
                    <div class="relative flex-1 sm:w-56">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama..."
                            class="w-full pl-9 pr-4 py-2.5 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kategori Filter Pills (Horizontal Scroll di HP) -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
                <button
                    type="button"
                    wire:click="$set('selectedCategory', null)"
                    class="px-3 py-1.5 rounded-lg whitespace-nowrap font-medium transition {{ is_null($selectedCategory) ? 'bg-primary-600 text-white shadow' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}"
                >
                    Semua
                </button>
                @foreach($this->categories as $cat)
                    <button
                        type="button"
                        wire:click="$set('selectedCategory', {{ $cat->id }})"
                        class="px-3 py-1.5 rounded-lg whitespace-nowrap font-medium transition {{ $selectedCategory === $cat->id ? 'bg-primary-600 text-white shadow' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}"
                    >
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>

            <!-- Tab Switcher Khusus Mobile (< lg) -->
            <div class="grid grid-cols-2 gap-2 lg:hidden pt-1 border-t border-gray-100 dark:border-gray-700">
                <button
                    type="button"
                    wire:click="$set('mobileView', 'catalog')"
                    class="py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 {{ $mobileView === 'catalog' ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}"
                >
                    <x-heroicon-o-squares-2x2 class="w-4 h-4" />
                    Katalog Produk
                </button>
                <button
                    type="button"
                    wire:click="$set('mobileView', 'cart')"
                    class="py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 relative {{ $mobileView === 'cart' ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}"
                >
                    <x-heroicon-o-shopping-cart class="w-4 h-4" />
                    Keranjang
                    @if($this->totalItemCount > 0)
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-red-500 text-white font-bold animate-pulse">
                            {{ $this->totalItemCount }}
                        </span>
                    @endif
                </button>
            </div>
        </div>

        <!-- Main Layout: 2 Columns on Desktop, Tabbed on Mobile -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 pb-20 lg:pb-0">
            <!-- Kolom Kiri: Katalog Produk (Desktop: 7 Cols, Mobile: Tampil jika mobileView == 'catalog') -->
            <div class="lg:col-span-7 space-y-4 {{ $mobileView === 'catalog' ? 'block' : 'hidden lg:block' }}">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 max-h-[640px] overflow-y-auto p-1">
                    @forelse ($this->availableProducts as $product)
                        <div
                            wire:click="addToCart({{ $product->id }})"
                            class="bg-white dark:bg-gray-800 p-3 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 cursor-pointer transition shadow-sm hover:shadow flex flex-col justify-between group active:scale-[0.98]"
                        >
                            <div>
                                <!-- Header Badge -->
                                <div class="flex justify-between items-start gap-1 mb-1.5">
                                    <span class="text-[11px] px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded font-mono font-semibold">
                                        {{ $product->code }}
                                    </span>
                                    <span class="text-[11px] px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 rounded-full font-bold">
                                        Stok: {{ $product->stock }}
                                    </span>
                                </div>

                                <!-- Nama & Kategori -->
                                <h4 class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-2 group-hover:text-primary-600 transition">
                                    {{ $product->name }}
                                </h4>
                                <div class="flex items-center gap-1 mt-1">
                                    <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $product->category?->name }}</span>
                                    @if($product->barcode)
                                        <span class="text-[10px] text-gray-400 font-mono">({{ $product->barcode }})</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Harga & Action -->
                            <div class="mt-3 pt-2.5 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                <span class="font-extrabold text-sm text-primary-600 dark:text-primary-400">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </span>
                                <span class="text-xs font-semibold px-2 py-1 bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400 rounded-lg group-hover:bg-primary-600 group-hover:text-white transition">
                                    + Tambah
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center text-gray-400">
                            <x-heroicon-o-inbox class="w-14 h-14 mx-auto mb-2 opacity-40" />
                            <p class="font-medium text-sm">Tidak ada produk yang cocok dengan pencarian atau stok kosong.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kolom Kanan: Keranjang & Pembayaran (Desktop: 5 Cols, Mobile: Tampil jika mobileView == 'cart') -->
            <div class="lg:col-span-5 space-y-4 {{ $mobileView === 'cart' ? 'block' : 'hidden lg:block' }}">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col h-full overflow-hidden">
                    <!-- Header Keranjang -->
                    <div class="p-3.5 sm:p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/20">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-sm sm:text-base">
                            <x-heroicon-o-shopping-cart class="w-5 h-5 text-primary-600" />
                            Keranjang Belanja ({{ $this->totalItemCount }})
                        </h3>
                        @if(count($cart) > 0)
                            <button
                                wire:click="clearCart"
                                wire:confirm="Yakin ingin mengosongkan seluruh keranjang belanja?"
                                class="text-xs text-red-600 hover:text-red-700 font-semibold flex items-center gap-1"
                            >
                                <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                Kosongkan
                            </button>
                        @endif
                    </div>

                    <!-- Input Pelanggan -->
                    <div class="p-3 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                        <input
                            type="text"
                            wire:model="customer_name"
                            placeholder="Nama Pelanggan (opsional)"
                            class="w-full px-3 py-2 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white text-xs sm:text-sm"
                        />
                    </div>

                    <!-- Daftar Item dalam Keranjang -->
                    <div class="flex-1 overflow-y-auto max-h-[300px] sm:max-h-[340px] p-3 space-y-2.5 divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($cart as $id => $item)
                            <div class="pt-2.5 first:pt-0 flex justify-between items-center gap-2">
                                <div class="flex-1 min-w-0 pr-1">
                                    <div class="font-semibold text-gray-900 dark:text-white text-xs sm:text-sm truncate">
                                        {{ $item['name'] }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                                    </div>
                                </div>

                                <!-- Quantity Controls (Besar, Ramah Sentuhan HP) -->
                                <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 p-1 rounded-xl">
                                    <button
                                        type="button"
                                        wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-white dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 flex items-center justify-center text-sm font-bold shadow-xs active:scale-95 transition"
                                    >-</button>
                                    <span class="w-7 text-center text-xs sm:text-sm font-bold">{{ $item['quantity'] }}</span>
                                    <button
                                        type="button"
                                        wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-white dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 flex items-center justify-center text-sm font-bold shadow-xs active:scale-95 transition"
                                    >+</button>
                                </div>

                                <div class="w-24 text-right font-extrabold text-xs sm:text-sm text-gray-900 dark:text-white">
                                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </div>

                                <button
                                    type="button"
                                    wire:click="removeFromCart({{ $id }})"
                                    class="text-gray-400 hover:text-red-500 p-1 rounded-lg"
                                >
                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                </button>
                            </div>
                        @empty
                            <div class="py-10 text-center text-gray-400 text-xs">
                                <x-heroicon-o-shopping-cart class="w-10 h-10 mx-auto mb-2 opacity-30" />
                                Keranjang masih kosong. Pilih produk di katalog atau scan barcode dengan HP.
                            </div>
                        @endforelse
                    </div>

                    <!-- Perhitungan & Form Checkout -->
                    <div class="p-3.5 sm:p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/80 rounded-b-2xl space-y-3">
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span class="font-medium">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-600 dark:text-gray-400">Diskon Toko (Rp)</span>
                            <input
                                type="number"
                                wire:model.live.debounce.300ms="discount"
                                placeholder="0"
                                class="w-28 text-right py-1 px-2.5 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-xs font-semibold"
                                min="0"
                            />
                        </div>

                        <div class="flex justify-between text-base font-extrabold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span>Grand Total</span>
                            <span class="text-primary-600 dark:text-primary-400 text-lg">
                                Rp {{ number_format($this->total, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Pilihan Metode Pembayaran -->
                        <div class="grid grid-cols-3 gap-2 pt-1">
                            @foreach(['cash' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer'] as $method => $label)
                                <button
                                    type="button"
                                    wire:click="$set('payment_method', '{{ $method }}')"
                                    class="py-2 text-xs font-bold rounded-xl border text-center transition {{ $payment_method === $method ? 'bg-primary-600 text-white border-primary-600 shadow-sm' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50' }}"
                                >
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Input Nominal Bayar & Kembalian jika Cash -->
                        @if($payment_method === 'cash')
                            <div class="p-2.5 bg-white dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 space-y-2">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Uang Diterima:</span>
                                    <input
                                        type="number"
                                        wire:model.live.debounce.300ms="paid_amount"
                                        class="w-36 text-right py-1.5 px-2.5 border rounded-lg font-bold dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:ring-2 focus:ring-primary-500"
                                        min="0"
                                    />
                                </div>
                                <div class="flex justify-between text-xs font-bold text-green-600 dark:text-green-400 pt-1 border-t border-dashed border-gray-200 dark:border-gray-600">
                                    <span>Kembalian:</span>
                                    <span class="text-sm">Rp {{ number_format($this->changeAmount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Tombol Selesaikan Transaksi -->
                        <button
                            type="button"
                            wire:click="checkout"
                            @disabled(empty($cart))
                            class="w-full py-3.5 px-4 bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-black rounded-xl text-sm sm:text-base shadow-lg shadow-green-600/30 transition flex items-center justify-center gap-2 active:scale-95"
                        >
                            <x-heroicon-o-check-circle class="w-5 h-5" />
                            Bayar Sekarang (Rp {{ number_format($this->total, 0, ',', '.') }})
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Floating Bottom Bar untuk Mobile (Jika Cart ada isinya dan sedang di tab Katalog) -->
        <div
            class="fixed bottom-0 inset-x-0 p-3 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-2xl lg:hidden z-20 transition-transform duration-300"
            x-show="$wire.mobileView === 'catalog' && {{ $this->totalItemCount }} > 0"
            x-transition:enter="transform transition ease-out duration-200"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
        >
            <div class="flex justify-between items-center gap-3 max-w-md mx-auto">
                <div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400">Total ({{ $this->totalItemCount }} item)</div>
                    <div class="text-base font-extrabold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($this->total, 0, ',', '.') }}
                    </div>
                </div>
                <button
                    type="button"
                    wire:click="$set('mobileView', 'cart')"
                    class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow flex items-center gap-1.5 active:scale-95"
                >
                    <x-heroicon-o-shopping-cart class="w-4 h-4" />
                    <span>Lihat Keranjang & Bayar</span>
                </button>
            </div>
        </div>

        <!-- Modal Scanner Kamera HP -->
        <div
            x-show="isScannerOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/80 backdrop-blur-xs"
            style="display: none;"
        >
            <div
                @click.away="closeScanner()"
                class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col"
            >
                <div class="p-4 bg-gray-900 text-white flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-camera class="w-5 h-5 text-green-400" />
                        <h3 class="font-bold text-sm sm:text-base">Scanner Kamera HP</h3>
                    </div>
                    <button
                        type="button"
                        @click="closeScanner()"
                        class="text-gray-400 hover:text-white p-1 rounded-lg"
                    >
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                <div class="p-4 space-y-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        Arahkan kamera ke barcode produk. Barang otomatis ditambahkan setelah bunyi beep!
                    </p>

                    <!-- Camera Viewfinder Box -->
                    <div class="relative w-full aspect-square bg-black rounded-xl overflow-hidden shadow-inner flex items-center justify-center">
                        <div id="reader" class="w-full h-full"></div>
                        <!-- Scan Target Frame overlay -->
                        <div class="pointer-events-none absolute inset-8 border-2 border-green-500/80 rounded-xl animate-pulse"></div>
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="closeScanner()"
                            class="w-full py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-800 dark:text-gray-200 rounded-xl text-xs sm:text-sm font-semibold transition"
                        >
                            Tutup Scanner
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Struk Pembayaran Terakhir (Receipt Card) -->
        @if($last_sale)
            <div class="p-4 bg-green-50 dark:bg-green-950/40 border border-green-200 dark:border-green-800 rounded-2xl space-y-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 bg-green-600 text-white rounded-lg">
                            <x-heroicon-o-check class="w-4 h-4" />
                        </span>
                        <div>
                            <h4 class="font-bold text-sm text-green-900 dark:text-green-200">Transaksi Selesai!</h4>
                            <p class="text-xs text-green-700 dark:text-green-400 font-mono">{{ $last_sale['invoice_number'] }} • {{ $last_sale['time'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Total Dibayar</div>
                        <div class="text-base font-extrabold text-green-700 dark:text-green-300">
                            Rp {{ number_format($last_sale['total'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-green-200 dark:border-green-800 flex justify-end gap-2">
                    <button
                        type="button"
                        onclick="window.print()"
                        class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-green-300 text-green-800 dark:text-green-300 rounded-lg text-xs font-semibold hover:bg-green-50 flex items-center gap-1"
                    >
                        <x-heroicon-o-printer class="w-4 h-4" />
                        Cetak Struk
                    </button>
                    <button
                        type="button"
                        wire:click="$set('last_sale', null)"
                        class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-semibold hover:bg-green-700"
                    >
                        Transaksi Baru &rarr;
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Alpine.js Audio & Camera Scanner Controller -->
    <script>
        function posApp() {
            return {
                isScannerOpen: false,
                html5QrCode: null,
                audioCtx: null,

                init() {
                    // Audio beep synthesizer
                    window.addEventListener('play-beep', () => this.playSynthBeep(880, 0.12));
                    window.addEventListener('play-error-beep', () => this.playSynthBeep(330, 0.25));
                    window.addEventListener('play-success-sound', () => {
                        this.playSynthBeep(523.25, 0.1);
                        setTimeout(() => this.playSynthBeep(659.25, 0.15), 100);
                    });
                },

                playSynthBeep(freq = 880, duration = 0.15) {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.frequency.value = freq;
                        osc.type = 'sine';
                        gain.gain.setValueAtTime(0.3, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
                        osc.start();
                        osc.stop(ctx.currentTime + duration);
                    } catch (e) {
                        // audio not supported or blocked
                    }
                },

                openScanner() {
                    this.isScannerOpen = true;
                    this.$nextTick(() => {
                        this.startCamera();
                    });
                },

                startCamera() {
                    if (typeof Html5Qrcode === 'undefined') {
                        alert('Library Barcode Scanner sedang dimuat, coba sesaat lagi.');
                        return;
                    }

                    this.html5QrCode = new Html5Qrcode("reader");
                    const config = {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0,
                    };

                    // Prioritaskan kamera belakang HP (environment)
                    this.html5QrCode.start(
                        { facingMode: "environment" },
                        config,
                        (decodedText) => {
                            // Haptic vibration feedback untuk HP
                            if (navigator.vibrate) {
                                navigator.vibrate(100);
                            }
                            this.playSynthBeep(880, 0.15);

                            // Panggil method Livewire langsung
                            @this.scanBarcodeDirect(decodedText);
                        },
                        (errorMessage) => {
                            // frame parsing error (normal saat mencari barcode)
                        }
                    ).catch(err => {
                        console.error("Gagal membuka kamera: ", err);
                        alert("Tidak dapat mengakses kamera: " + (err.message || err));
                        this.isScannerOpen = false;
                    });
                },

                closeScanner() {
                    if (this.html5QrCode) {
                        this.html5QrCode.stop().then(() => {
                            this.html5QrCode.clear();
                            this.html5QrCode = null;
                        }).catch(err => {
                            console.error("Gagal menutup scanner: ", err);
                        });
                    }
                    this.isScannerOpen = false;
                }
            };
        }
    </script>
</x-filament-panels::page>
