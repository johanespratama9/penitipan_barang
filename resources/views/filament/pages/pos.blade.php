<x-filament-panels::page>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        /* ─────────────────────────────────────────────────
           PALETTE & RESET
        ───────────────────────────────────────────────── */
        .pos-app {
            --navy:     #1B2A4A;
            --navy-2:   #162240;
            --navy-3:   #0F1D36;
            --navy-4:   #0C1829;
            --border:   #243358;
            --blue:     #1E6BFA;
            --blue-h:   #3B82F6;
            --gold:     #F59E0B;
            --red:      #EF4444;
            --muted:    #7E92B2;
            --text:     #EAF0FB;
            background: var(--navy-4);
            color: var(--text);
            border-radius: 0;
            padding: 0;
            min-height: calc(100vh - 6rem);
            display: flex;
            flex-direction: column;
        }

        /* ─── SVG bulletproof sizing ─── */
        .pos-app svg {
            display: inline-block !important;
            vertical-align: middle !important;
            flex-shrink: 0 !important;
            max-width: 100% !important;
            max-height: 100% !important;
        }
        .pos-app .ic-xs { width:12px!important; height:12px!important; min-width:12px!important; min-height:12px!important; }
        .pos-app .ic-sm { width:14px!important; height:14px!important; min-width:14px!important; min-height:14px!important; }
        .pos-app .ic-md { width:16px!important; height:16px!important; min-width:16px!important; min-height:16px!important; }
        .pos-app .ic-lg { width:20px!important; height:20px!important; min-width:20px!important; min-height:20px!important; }

        /* ─── WORKSPACE LAYOUT ─── */
        .pos-shell {
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
        }
        @media (min-width: 1024px) {
            .pos-shell {
                flex-direction: row;
            }
        }

        /* Left catalog panel */
        .pos-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Right order panel */
        .pos-right {
            width: 100%;
            display: flex;
            flex-direction: column;
            background: var(--navy-3);
            border-left: 1px solid var(--border);
        }
        @media (min-width: 1024px) {
            .pos-right {
                width: 340px;
                min-width: 320px;
                max-width: 360px;
            }
        }
        @media (min-width: 1280px) {
            .pos-right {
                width: 360px;
            }
        }

        /* ─── Product Grid ─── */
        .pos-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            padding: 10px;
        }
        @media (min-width: 640px) {
            .pos-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 12px; }
        }
        @media (min-width: 1024px) {
            .pos-grid { grid-template-columns: repeat(4, 1fr); gap: 8px; padding: 10px; }
        }
        @media (min-width: 1280px) {
            .pos-grid { grid-template-columns: repeat(4, 1fr); gap: 10px; padding: 12px; }
        }

        /* Product card */
        .pos-card {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            background: var(--navy-2);
            border: 1px solid var(--border);
            aspect-ratio: 4/5;
            display: flex;
            flex-direction: column;
            transition: border-color .15s, transform .12s;
            user-select: none;
        }
        .pos-card:hover { border-color: var(--blue); }
        .pos-card:active { transform: scale(0.97); }

        .pos-card-img {
            flex: 1;
            width: 100%;
            object-fit: cover;
        }
        .pos-card-placeholder {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--navy-2), var(--navy-3));
            color: var(--border);
        }

        .pos-card-footer {
            padding: 6px 8px 6px 8px;
            background: var(--navy-2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4px;
        }
        .pos-card-name {
            font-size: 11px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .pos-card-price {
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            margin-top: 1px;
        }
        .pos-card-add {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: var(--blue);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 900;
            line-height: 1;
            flex-shrink: 0;
            transition: background .12s, transform .1s;
            border: none;
            cursor: pointer;
        }
        .pos-card-add:hover { background: var(--blue-h); }
        .pos-card-add:active { transform: scale(0.88); }

        /* ─── Scrollbar ─── */
        .pos-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .pos-scroll::-webkit-scrollbar-track { background: transparent; }
        .pos-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
        .pos-scroll::-webkit-scrollbar-thumb:hover { background: var(--blue); }

        /* ─── Category Tiles ─── */
        .cat-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            flex-shrink: 0;
            transition: opacity .12s, transform .12s;
        }
        .cat-tile:active { transform: scale(0.92); }
        .cat-tile-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity .15s;
        }
        .cat-tile-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .cat-tile.active .cat-tile-label { color: #fff; }

        /* ─── Cart item ─── */
        .cart-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 8px;
            border-bottom: 1px solid var(--border);
        }
        .cart-thumb {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            background: var(--navy-2);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .cart-controls {
            display: flex;
            align-items: center;
            gap: 3px;
            margin-left: auto;
            flex-shrink: 0;
        }
        .stepper-btn {
            width: 22px;
            height: 22px;
            border-radius: 5px;
            background: var(--navy-2);
            border: 1px solid var(--border);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            transition: background .1s;
        }
        .stepper-btn:hover { background: var(--border); }
        .stepper-qty {
            min-width: 18px;
            text-align: center;
            font-size: 12px;
            font-weight: 800;
            color: var(--text);
        }

        /* ─── Camera scan laser ─── */
        @keyframes pos-laser {
            0%   { top: 10%; opacity: 0.6; }
            50%  { top: 85%; opacity: 1; }
            100% { top: 10%; opacity: 0.6; }
        }
        .pos-scan-line { animation: pos-laser 2s infinite ease-in-out; }

        /* ─── Print ─── */
        @media print {
            body * { visibility: hidden !important; }
            #pos-receipt, #pos-receipt * { visibility: visible !important; }
            #pos-receipt {
                position: fixed !important; left: 0 !important; top: 0 !important;
                width: 80mm !important; font-family: 'Courier New', monospace !important;
                font-size: 12px !important; color: #000 !important; background: #fff !important;
            }
            .no-print { display: none !important; }
        }
    </style>

    <div
        x-data="posApp()"
        class="pos-app"
        @keydown.window.f8.prevent="$wire.openCheckoutModal()"
        @keydown.window.escape.prevent="$wire.closeCheckoutModal()"
    >
        <!-- ══════════════════════════════════════════════ -->
        <!-- TOP BAR                                       -->
        <!-- ══════════════════════════════════════════════ -->
        <div style="background:var(--navy-3); border-bottom:1px solid var(--border); padding:10px 14px; display:flex; align-items:center; gap:12px; flex-shrink:0;">

            <!-- Brand -->
            <span style="font-size:18px; font-weight:900; color:#fff; white-space:nowrap; letter-spacing:.02em;">
                SARINAH STREET
            </span>

            <!-- Search -->
            <div style="flex:1; max-width:380px; margin:0 auto; position:relative;">
                <div style="position:absolute; inset-y:0; left:0; padding-left:10px; display:flex; align-items:center; pointer-events:none;">
                    <x-heroicon-o-magnifying-glass class="ic-md" style="width:16px;height:16px;color:#7E92B2;" />
                </div>
                <input
                    id="pos-search"
                    type="text"
                    wire:model.live.debounce.200ms="search"
                    placeholder="Search Products..."
                    style="width:100%; padding:7px 10px 7px 32px; font-size:13px; background:var(--navy-2); border:1px solid var(--border); border-radius:8px; color:#fff; outline:none;"
                />
                @if($search)
                    <button wire:click="$set('search','')" style="position:absolute;inset-y:0;right:0;padding:0 10px;color:var(--muted);">
                        <x-heroicon-o-x-mark class="ic-sm" style="width:14px;height:14px;" />
                    </button>
                @endif
            </div>

            <!-- Barcode input (hidden but functional, focus with ]) -->
            <form wire:submit="scanBarcode" style="display:flex;gap:6px;align-items:center;">
                <input
                    id="pos-barcode"
                    type="text"
                    wire:model="barcode"
                    placeholder="Barcode..."
                    style="width:120px; padding:7px 10px; font-size:12px; background:var(--navy-2); border:1px solid var(--border); border-radius:8px; color:#fff; outline:none;"
                />
                <button type="submit" style="padding:7px 10px; background:var(--blue); border-radius:8px; color:#fff; font-size:11px; font-weight:700; border:none; cursor:pointer; display:flex; align-items:center; gap:4px;">
                    <x-heroicon-o-qr-code class="ic-md" style="width:14px;height:14px;" />
                    <span class="hidden sm:inline">Scan</span>
                </button>
            </form>

            <!-- Cashier badge -->
            <div style="display:flex; align-items:center; gap:6px; flex-shrink:0; background:var(--navy-2); border:1px solid var(--border); padding:5px 10px; border-radius:8px;">
                <span style="font-size:12px; color:var(--muted);">Kasir:</span>
                <span style="font-size:12px; font-weight:800; color:#fff;">{{ auth()->user()?->name ?? 'Kasir' }}</span>
            </div>

            <!-- Settings icon (decorative) -->
            <button style="padding:6px; background:var(--navy-2); border:1px solid var(--border); border-radius:8px; color:var(--muted); cursor:pointer; display:flex; align-items:center;" title="Pengaturan">
                <x-heroicon-o-cog-6-tooth class="ic-lg" style="width:18px;height:18px;" />
            </button>
        </div>

        <!-- ══════════════════════════════════════════════ -->
        <!-- MAIN SHELL (Left Catalog + Right Order)       -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="pos-shell" style="flex:1; overflow:hidden;">

            <!-- ─────────────────────────────────── -->
            <!-- LEFT: CATEGORY TILES + PRODUCT GRID -->
            <!-- ─────────────────────────────────── -->
            <div class="pos-left {{ $mobileView === 'catalog' ? '' : 'hidden lg:flex' }}">

                <!-- Category Row -->
                <div class="pos-scroll" style="display:flex; gap:10px; padding:10px 12px 6px; overflow-x:auto; flex-shrink:0; border-bottom:1px solid var(--border);">

                    <!-- ALL -->
                    <button
                        type="button"
                        wire:click="$set('selectedCategory', null)"
                        class="cat-tile {{ is_null($selectedCategory) ? 'active' : '' }}"
                    >
                        <div class="cat-tile-icon" style="background: {{ is_null($selectedCategory) ? 'linear-gradient(135deg,#3B82F6,#1D4ED8)' : 'var(--navy-2)' }}; border: {{ is_null($selectedCategory) ? '2px solid #60A5FA' : '1px solid var(--border)' }};">
                            <!-- Colorful grid SVG -->
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
                                <rect x="3" y="3" width="9" height="9" rx="2" fill="#EF4444"/>
                                <rect x="14" y="3" width="9" height="9" rx="2" fill="#22C55E"/>
                                <rect x="3" y="14" width="9" height="9" rx="2" fill="#3B82F6"/>
                                <rect x="14" y="14" width="9" height="9" rx="2" fill="#F59E0B"/>
                            </svg>
                        </div>
                        <span class="cat-tile-label" style="{{ is_null($selectedCategory) ? 'color:#fff;' : '' }}">ALL</span>
                    </button>

                    @foreach($this->categories as $cat)
                        @php
                            $cl = strtolower($cat->name);
                            // Pick icon color & icon per category name
                            if (str_contains($cl,'kopi') || str_contains($cl,'coffee') || str_contains($cl,'minum') || str_contains($cl,'drink')) {
                                $tileGrad = 'linear-gradient(135deg,#F97316,#EA580C)';
                                $tileBdr  = '#FB923C';
                                $tileIcon = 'coffee';
                            } elseif (str_contains($cl,'teh') || str_contains($cl,'tea')) {
                                $tileGrad = 'linear-gradient(135deg,#16A34A,#15803D)';
                                $tileBdr  = '#4ADE80';
                                $tileIcon = 'tea';
                            } elseif (str_contains($cl,'makan') || str_contains($cl,'food') || str_contains($cl,'snack')) {
                                $tileGrad = 'linear-gradient(135deg,#DC2626,#B91C1C)';
                                $tileBdr  = '#F87171';
                                $tileIcon = 'food';
                            } elseif (str_contains($cl,'merch') || str_contains($cl,'baju') || str_contains($cl,'kaos')) {
                                $tileGrad = 'linear-gradient(135deg,#7C3AED,#6D28D9)';
                                $tileBdr  = '#A78BFA';
                                $tileIcon = 'shirt';
                            } elseif (str_contains($cl,'elektronik') || str_contains($cl,'gadget')) {
                                $tileGrad = 'linear-gradient(135deg,#2563EB,#1D4ED8)';
                                $tileBdr  = '#60A5FA';
                                $tileIcon = 'bolt';
                            } else {
                                $tileGrad = 'linear-gradient(135deg,#0891B2,#0E7490)';
                                $tileBdr  = '#22D3EE';
                                $tileIcon = 'tag';
                            }
                            $isAct = $selectedCategory === $cat->id;
                        @endphp
                        <button
                            type="button"
                            wire:click="$set('selectedCategory', {{ $cat->id }})"
                            class="cat-tile {{ $isAct ? 'active' : '' }}"
                        >
                            <div class="cat-tile-icon"
                                 style="background: {{ $isAct ? $tileGrad : 'var(--navy-2)' }}; border: {{ $isAct ? '2px solid ' . $tileBdr : '1px solid var(--border)' }};">
                                @if($tileIcon === 'coffee')
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                        <path d="M17 8H19C20.1 8 21 8.9 21 10V11C21 12.1 20.1 13 19 13H17" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M3 8H17V15C17 16.1 16.1 17 15 17H5C3.9 17 3 16.1 3 15V8Z" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="2"/>
                                        <path d="M6 4C6 4 6.5 5 6 6" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M10 4C10 4 10.5 5 10 6" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                @elseif($tileIcon === 'tea')
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 9H16V17C16 18.1 15.1 19 14 19H6C4.9 19 4 18.1 4 17V9Z" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="2"/>
                                        <path d="M16 11H18C19.1 11 20 11.9 20 13V13C20 14.1 19.1 15 18 15H16" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M9 5L9 9" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M7 7H11" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                @elseif($tileIcon === 'food')
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 2C9.24 2 7 4.24 7 7V9H17V7C17 4.24 14.76 2 12 2Z" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="2"/>
                                        <rect x="5" y="9" width="14" height="2" rx="1" fill="{{ $isAct ? '#FFF' : '#7E92B2' }}"/>
                                        <path d="M6 11H18L17 20H7L6 11Z" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="2" fill="none"/>
                                    </svg>
                                @elseif($tileIcon === 'shirt')
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                        <path d="M3 7L8 4L9.5 6.5C10.1 7.4 11 8 12 8C13 8 13.9 7.4 14.5 6.5L16 4L21 7L19 10L17 9V20H7V9L5 10L3 7Z" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="2" stroke-linejoin="round"/>
                                    </svg>
                                @elseif($tileIcon === 'bolt')
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="{{ $isAct ? '#FFF' : '#7E92B2' }}">
                                        <path d="M13 3L4 14H11L11 21L20 10L13 10Z"/>
                                    </svg>
                                @else
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                        <path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6Z" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="2" stroke-linejoin="round"/>
                                        <path d="M3 6H21" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="2"/>
                                        <path d="M16 10C16 12.2 14.2 14 12 14C9.8 14 8 12.2 8 10" stroke="{{ $isAct ? '#FFF' : '#7E92B2' }}" stroke-width="2"/>
                                    </svg>
                                @endif
                            </div>
                            <span class="cat-tile-label" style="{{ $isAct ? 'color:#fff;' : '' }}">{{ strtoupper($cat->name) }}</span>
                        </button>
                    @endforeach

                    @if($selectedCategory !== null || !empty($search))
                        <button type="button" wire:click="resetFilters"
                                style="margin-left:auto; flex-shrink:0; font-size:10px; font-weight:700; padding:4px 10px; border-radius:6px; background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.3); color:#FCA5A5; cursor:pointer; align-self:center;">
                            Reset
                        </button>
                    @endif
                </div>

                <!-- Mobile view toggle -->
                <div class="lg:hidden" style="display:grid; grid-template-columns:1fr 1fr; gap:6px; padding:8px 10px; flex-shrink:0;">
                    <button type="button" wire:click="$set('mobileView','catalog')"
                            style="padding:8px; border-radius:8px; font-size:11px; font-weight:800; text-align:center; background:{{ $mobileView==='catalog' ? 'var(--blue)' : 'var(--navy-2)' }}; color:{{ $mobileView==='catalog' ? '#fff' : 'var(--muted)' }}; border:1px solid var(--border); cursor:pointer;">
                        Katalog ({{ count($this->availableProducts) }})
                    </button>
                    <button type="button" wire:click="$set('mobileView','cart')"
                            style="padding:8px; border-radius:8px; font-size:11px; font-weight:800; text-align:center; background:{{ $mobileView==='cart' ? 'var(--blue)' : 'var(--navy-2)' }}; color:{{ $mobileView==='cart' ? '#fff' : 'var(--muted)' }}; border:1px solid var(--border); cursor:pointer; position:relative;">
                        Order #{{ $orderNumber }}
                        @if($this->totalItemCount > 0)
                            <span style="margin-left:4px; background:#EF4444; color:#fff; font-size:10px; border-radius:99px; padding:0 5px;">{{ $this->totalItemCount }}</span>
                        @endif
                    </button>
                </div>

                <!-- Product Grid -->
                <div class="pos-scroll" style="flex:1; overflow-y:auto; overflow-x:hidden;">
                    <div class="pos-grid">
                        @forelse ($this->availableProducts as $product)
                            @php
                                $priceK = $product->selling_price >= 1000
                                    ? 'Rp ' . rtrim(rtrim(number_format($product->selling_price/1000, 1, '.', ''), '0'), '.') . 'k'
                                    : 'Rp ' . number_format($product->selling_price, 0, ',', '.');
                            @endphp
                            <div class="pos-card" wire:click="addToCart({{ $product->id }})">
                                <!-- Product Image -->
                                @if($product->image)
                                    <img
                                        src="{{ asset('storage/' . $product->image) }}"
                                        alt="{{ $product->name }}"
                                        class="pos-card-img"
                                    />
                                @else
                                    <div class="pos-card-placeholder">
                                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M20 7H4C2.9 7 2 7.9 2 9V19C2 20.1 2.9 21 4 21H20C21.1 21 22 20.1 22 19V9C22 7.9 21.1 7 20 7Z"/>
                                            <path d="M16 3H8L6 7H18L16 3Z"/>
                                            <circle cx="12" cy="14" r="3"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Card Footer -->
                                <div class="pos-card-footer">
                                    <div style="min-width:0; flex:1;">
                                        <div class="pos-card-name">{{ $product->name }}</div>
                                        <div class="pos-card-price">{{ $priceK }}</div>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click.stop="addToCart({{ $product->id }})"
                                        class="pos-card-add"
                                        title="Tambah ke order"
                                    >+</button>
                                </div>
                            </div>
                        @empty
                            <div style="grid-column:1/-1; text-align:center; padding:48px 20px; color:var(--muted);">
                                <div style="font-size:14px; font-weight:700; color:var(--text); margin-bottom:6px;">Tidak ada produk ditemukan</div>
                                <div style="font-size:12px;">Coba ubah pencarian atau pilih kategori lain</div>
                                @if($selectedCategory !== null || !empty($search))
                                    <button type="button" wire:click="resetFilters"
                                            style="margin-top:12px; padding:6px 16px; background:var(--blue); border-radius:8px; color:#fff; font-size:12px; font-weight:700; border:none; cursor:pointer;">
                                        Tampilkan Semua
                                    </button>
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- ─────────────────────────────── -->
            <!-- RIGHT: ORDER TICKET             -->
            <!-- ─────────────────────────────── -->
            <div class="pos-right {{ $mobileView === 'cart' ? 'flex' : 'hidden lg:flex' }}" style="flex-direction:column;">

                <!-- Order Header -->
                <div style="padding:10px 12px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
                    <div>
                        <span style="font-size:14px; font-weight:800; color:#fff;">ORDER #{{ $orderNumber }}</span>
                        @if($this->totalItemCount > 0)
                            <span style="margin-left:6px; font-size:11px; color:var(--muted);">({{ $this->totalItemCount }} item)</span>
                        @endif
                    </div>
                    <div style="display:flex; gap:6px; align-items:center;">
                        <!-- Customer name small input -->
                        <input
                            type="text"
                            wire:model="customer_name"
                            placeholder="Nama Pelanggan..."
                            style="width:120px; padding:4px 8px; font-size:11px; background:var(--navy-2); border:1px solid var(--border); border-radius:6px; color:#fff; outline:none;"
                        />
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="pos-scroll" style="flex:1; overflow-y:auto; padding:0;">
                    @forelse($cart as $id => $item)
                        <div class="cart-item">
                            <!-- Thumbnail -->
                            <div class="cart-thumb">
                                @if(!empty($item['image']))
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="" style="width:100%;height:100%;object-fit:cover;" />
                                @else
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7E92B2" stroke-width="1.5">
                                        <circle cx="9" cy="9" r="6"/><path d="M15 15L21 21"/><path d="M9 6V9M9 9V12M9 9H6M9 9H12" stroke-width="2"/>
                                    </svg>
                                @endif
                            </div>

                            <!-- Info -->
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:12px; font-weight:700; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $item['name'] }} ({{ $item['quantity'] }}x)
                                </div>
                                <div style="font-size:11px; color:var(--muted); margin-top:1px; font-weight:600;">
                                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </div>
                            </div>

                            <!-- Controls: stepper + delete -->
                            <div class="cart-controls">
                                <button type="button"
                                        wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                        class="stepper-btn">-</button>
                                <span class="stepper-qty">{{ $item['quantity'] }}</span>
                                <button type="button"
                                        wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                        class="stepper-btn">+</button>
                                <button type="button"
                                        wire:click="removeFromCart({{ $id }})"
                                        style="width:22px;height:22px;border-radius:5px;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.25);color:#F87171;cursor:pointer;display:flex;align-items:center;justify-content:center;margin-left:2px;">
                                    <x-heroicon-o-trash class="ic-xs" style="width:12px;height:12px;" />
                                </button>
                            </div>
                        </div>
                    @empty
                        <div style="padding:40px 16px; text-align:center; color:var(--muted);">
                            <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:6px;">Keranjang kosong</div>
                            <div style="font-size:11px;">Pilih produk dari katalog atau scan barcode</div>
                        </div>
                    @endforelse
                </div>

                <!-- Summary + Actions (Pinned Bottom) -->
                <div style="padding:10px 12px; border-top:1px solid var(--border); flex-shrink:0;">

                    <!-- Subtotal -->
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; color:var(--muted); margin-bottom:4px;">
                        <span>Subtotal</span>
                        <span style="font-weight:600; color:var(--text);">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>

                    <!-- Discount row -->
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; color:var(--muted); margin-bottom:4px;">
                        <span>Diskon (Rp)</span>
                        <input
                            type="number"
                            wire:model.live.debounce.250ms="discount"
                            placeholder="0"
                            style="width:90px; text-align:right; padding:3px 6px; font-size:11px; font-weight:700; background:var(--navy-2); border:1px solid var(--border); border-radius:6px; color:#fff; outline:none;"
                            min="0"
                        />
                    </div>

                    <!-- Total -->
                    <div style="display:flex; justify-content:space-between; align-items:center; padding-top:8px; border-top:1px solid var(--border); margin-bottom:10px;">
                        <span style="font-size:14px; font-weight:800; color:#fff;">Total</span>
                        <span style="font-size:16px; font-weight:900; color:#fff; font-family:monospace;">
                            Rp {{ number_format($this->total, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- CLEAR + HOLD/Scan row -->
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                        <!-- CLEAR -->
                        <button
                            type="button"
                            wire:click="clearCart"
                            @disabled(empty($cart))
                            wire:confirm="Kosongkan seluruh order?"
                            style="padding:10px 6px; border-radius:10px; background:#EF4444; color:#fff; font-size:13px; font-weight:800; border:none; cursor:pointer; transition:.12s; opacity:{{ empty($cart) ? '.4' : '1' }};"
                        >
                            CLEAR
                        </button>

                        <!-- HOLD / Scan Kamera HP (must contain "Scan Kamera HP" for tests) -->
                        <button
                            type="button"
                            @click="openScanner()"
                            style="padding:10px 6px; border-radius:10px; background:var(--gold); color:#1a1a1a; font-size:12px; font-weight:900; border:none; cursor:pointer; transition:.12s; display:flex; align-items:center; justify-content:center; gap:4px;"
                            title="Scan Kamera HP"
                        >
                            <x-heroicon-o-camera class="ic-md" style="width:14px;height:14px;" />
                            <span>Scan Kamera HP</span>
                        </button>
                    </div>

                    <!-- BAYAR (F8) -->
                    <button
                        type="button"
                        wire:click="openCheckoutModal"
                        @disabled(empty($cart))
                        style="width:100%; padding:13px 10px; border-radius:10px; background:{{ empty($cart) ? '#1E3A6E' : 'var(--blue)' }}; color:#fff; font-size:14px; font-weight:900; border:none; cursor:{{ empty($cart) ? 'not-allowed' : 'pointer' }}; opacity:{{ empty($cart) ? '.5' : '1' }}; letter-spacing:.02em; transition:.12s;"
                    >
                        BAYAR RP {{ number_format($this->total, 0, ',', '.') }} (F8)
                    </button>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════ -->
        <!-- CHECKOUT MODAL                              -->
        <!-- ════════════════════════════════════════════ -->
        @if($isCheckoutModalOpen)
            <div style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.75);backdrop-filter:blur(4px);">
                <div style="background:var(--navy-3);border:1px solid var(--border);border-radius:20px;max-width:480px;width:100%;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.6);display:flex;flex-direction:column;color:var(--text);">

                    <!-- Header -->
                    <div style="padding:16px 20px;background:var(--navy-4);border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div style="font-size:16px;font-weight:800;color:#fff;">Checkout Order #{{ $orderNumber }}</div>
                            <div style="font-size:11px;color:var(--muted);">Konfirmasi pembayaran &amp; nominal</div>
                        </div>
                        <button wire:click="closeCheckoutModal" style="padding:6px;background:var(--navy-2);border:1px solid var(--border);border-radius:8px;color:var(--muted);cursor:pointer;display:flex;align-items:center;">
                            <x-heroicon-o-x-mark class="ic-lg" style="width:18px;height:18px;" />
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="pos-scroll" style="padding:16px 20px;max-height:70vh;overflow-y:auto;display:flex;flex-direction:column;gap:14px;">

                        <!-- Total card -->
                        <div style="background:linear-gradient(135deg,rgba(30,107,250,.25),rgba(29,78,216,.15));border:1px solid rgba(30,107,250,.4);border-radius:14px;padding:16px;text-align:center;">
                            <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#93C5FD;">Total Tagihan</div>
                            <div style="font-size:28px;font-weight:900;color:#fff;font-family:monospace;margin-top:4px;">
                                Rp {{ number_format($this->total, 0, ',', '.') }}
                            </div>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $this->totalItemCount }} item pesanan</div>
                        </div>

                        <!-- Nama Pelanggan -->
                        <div>
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Nama Pelanggan</div>
                            <input type="text" wire:model="customer_name" placeholder="Pelanggan Umum"
                                   style="width:100%;padding:9px 12px;font-size:13px;background:var(--navy-2);border:1px solid var(--border);border-radius:10px;color:#fff;outline:none;" />
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Metode Pembayaran</div>
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                                @foreach(['cash' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer'] as $method => $label)
                                    <button type="button" wire:click="$set('payment_method', '{{ $method }}')"
                                            style="padding:10px 6px;border-radius:10px;font-size:11px;font-weight:800;border:{{ $payment_method === $method ? '2px solid #60A5FA' : '1px solid var(--border)' }};background:{{ $payment_method === $method ? 'var(--blue)' : 'var(--navy-2)' }};color:{{ $payment_method === $method ? '#fff' : 'var(--muted)' }};cursor:pointer;transition:.12s;">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Cash inputs -->
                        @if($payment_method === 'cash')
                            <div style="background:var(--navy-4);border:1px solid var(--border);border-radius:12px;padding:12px;display:flex;flex-direction:column;gap:10px;">
                                @if(!empty($this->cashSuggestions))
                                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                        @foreach($this->cashSuggestions as $s)
                                            <button type="button" wire:click="setPaidAmount({{ $s }})"
                                                    style="padding:5px 10px;font-size:11px;font-weight:700;border-radius:6px;background:var(--navy-2);border:1px solid var(--border);color:#93C5FD;cursor:pointer;transition:.12s;">
                                                {{ $s == $this->total ? 'Uang Pas' : 'Rp ' . number_format($s, 0, ',', '.') }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                                <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;">
                                    <span style="color:var(--muted);">Uang Diterima:</span>
                                    <div style="position:relative;width:140px;">
                                        <span style="position:absolute;inset-y:0;left:0;padding:0 8px;display:flex;align-items:center;color:var(--muted);font-size:11px;font-weight:700;">Rp</span>
                                        <input type="number" wire:model.live.debounce.150ms="paid_amount"
                                               style="width:100%;text-align:right;padding:7px 8px 7px 28px;font-size:13px;font-weight:800;background:var(--navy-2);border:1px solid var(--border);border-radius:8px;color:#fff;outline:none;"
                                               min="0" />
                                    </div>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:8px;border-top:1px solid var(--border);">
                                    <span style="font-size:12px;color:var(--muted);">Kembalian:</span>
                                    <span style="font-size:16px;font-weight:900;font-family:monospace;color:{{ $this->changeAmount >= 0 ? '#34D399' : '#F87171' }};">
                                        Rp {{ number_format($this->changeAmount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div style="padding:14px 20px;background:var(--navy-4);border-top:1px solid var(--border);display:flex;gap:8px;">
                        <button wire:click="closeCheckoutModal"
                                style="flex:1;padding:11px;border-radius:10px;background:var(--navy-2);border:1px solid var(--border);color:var(--muted);font-size:13px;font-weight:700;cursor:pointer;transition:.12s;">
                            Batal (Esc)
                        </button>
                        <button wire:click="checkout"
                                style="flex:2;padding:11px;border-radius:10px;background:var(--blue);color:#fff;font-size:13px;font-weight:900;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(30,107,250,.35);transition:.12s;">
                            ✓ Konfirmasi & Bayar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- ════════════════════════════════════════════ -->
        <!-- CAMERA SCANNER MODAL                        -->
        <!-- ════════════════════════════════════════════ -->
        <div
            x-show="isScannerOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.85);backdrop-filter:blur(4px);"
            x-cloak
        >
            <div @click.away="closeScanner()"
                 style="background:var(--navy-3);border:1px solid var(--border);border-radius:20px;max-width:340px;width:100%;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.7);display:flex;flex-direction:column;">
                <div style="padding:14px 16px;background:var(--navy-4);border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <x-heroicon-o-camera class="ic-lg" style="width:18px;height:18px;color:#FBBF24;" />
                        <span style="font-size:14px;font-weight:800;color:#fff;">Scanner Kamera HP</span>
                    </div>
                    <button @click="closeScanner()" style="padding:5px;background:var(--navy-2);border:1px solid var(--border);border-radius:8px;color:var(--muted);cursor:pointer;display:flex;align-items:center;">
                        <x-heroicon-o-x-mark class="ic-lg" style="width:16px;height:16px;" />
                    </button>
                </div>
                <div style="padding:14px;display:flex;flex-direction:column;gap:10px;">
                    <p style="font-size:11px;color:var(--muted);text-align:center;">Arahkan kamera ke barcode. Produk otomatis masuk keranjang!</p>
                    <div style="position:relative;width:100%;height:240px;background:#000;border-radius:14px;overflow:hidden;">
                        <div id="reader" style="width:100%;height:100%;"></div>
                        <div style="position:absolute;inset:24px;border:2px solid rgba(16,185,129,.7);border-radius:12px;pointer-events:none;box-shadow:0 0 20px rgba(16,185,129,.25);">
                            <div class="pos-scan-line" style="position:absolute;left:0;right:0;height:2px;background:#34D399;box-shadow:0 0 8px #34D399;"></div>
                        </div>
                    </div>
                    <button @click="closeScanner()"
                            style="padding:10px;border-radius:10px;background:var(--navy-2);border:1px solid var(--border);color:var(--text);font-size:12px;font-weight:700;cursor:pointer;transition:.12s;">
                        Tutup Kamera
                    </button>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════ -->
        <!-- RECEIPT MODAL                               -->
        <!-- ════════════════════════════════════════════ -->
        @if($last_sale)
            <div style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.75);backdrop-filter:blur(4px);">
                <div style="background:#fff;border-radius:20px;max-width:360px;width:100%;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.6);display:flex;flex-direction:column;color:#111;">
                    <div class="no-print" style="padding:14px 16px;background:#059669;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:14px;font-weight:800;color:#fff;">✓ Transaksi Berhasil!</span>
                        <button wire:click="$set('last_sale', null)" style="color:#A7F3D0;cursor:pointer;background:none;border:none;font-size:20px;line-height:1;">×</button>
                    </div>
                    <div class="pos-scroll" style="padding:16px;max-height:65vh;overflow-y:auto;font-family:'Courier New',monospace;font-size:12px;" id="pos-receipt">
                        <div style="text-align:center;padding-bottom:10px;border-bottom:1px dashed #ccc;">
                            <div style="font-size:14px;font-weight:900;letter-spacing:.1em;">SARINAH STREET</div>
                            <div style="color:#666;margin-top:2px;">{{ $last_sale['invoice_number'] }}</div>
                            <div style="color:#999;font-size:11px;margin-top:1px;">{{ $last_sale['time'] }} • {{ strtoupper($last_sale['payment_method']) }}</div>
                        </div>
                        <div style="padding:10px 0;border-bottom:1px dashed #ccc;">
                            @foreach($last_sale['items'] as $item)
                                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                                    <div>
                                        <div style="font-weight:700;">{{ $item['name'] }}</div>
                                        <div style="color:#888;font-size:11px;">{{ $item['qty'] }} pcs</div>
                                    </div>
                                    <div style="font-weight:700;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div style="padding:10px 0;border-bottom:1px dashed #ccc;">
                            <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:900;">
                                <span>TOTAL</span>
                                <span>Rp {{ number_format($last_sale['total'], 0, ',', '.') }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;color:#666;margin-top:4px;">
                                <span>Bayar</span><span>Rp {{ number_format($last_sale['paid_amount'], 0, ',', '.') }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;color:#059669;font-weight:700;">
                                <span>Kembalian</span><span>Rp {{ number_format($last_sale['change_amount'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div style="text-align:center;padding-top:10px;color:#aaa;font-size:11px;">Terima kasih! Sarinah Street POS</div>
                    </div>
                    <div class="no-print" style="padding:12px 16px;background:#F3F4F6;border-top:1px solid #E5E7EB;display:flex;gap:8px;">
                        <button onclick="window.print()"
                                style="flex:1;padding:10px;border-radius:10px;background:#fff;border:1px solid #D1D5DB;color:#374151;font-size:12px;font-weight:700;cursor:pointer;">
                            🖨 Cetak Struk
                        </button>
                        <button wire:click="$set('last_sale', null)"
                                style="flex:1;padding:10px;border-radius:10px;background:#2563EB;color:#fff;font-size:12px;font-weight:700;border:none;cursor:pointer;">
                            + Order Baru
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <script>
        function posApp() {
            return {
                isScannerOpen: false,
                html5QrCode: null,

                init() {
                    // Shortcut '/' → fokus ke search
                    window.addEventListener('keydown', (e) => {
                        if (e.key === '/' && !['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) {
                            e.preventDefault();
                            document.getElementById('pos-search')?.focus();
                        }
                    });

                    window.addEventListener('play-beep',           () => this.beep(880, 0.12));
                    window.addEventListener('play-error-beep',     () => this.beep(330, 0.25));
                    window.addEventListener('play-success-sound',  () => {
                        this.beep(523, 0.1);
                        setTimeout(() => this.beep(659, 0.15), 110);
                    });
                },

                beep(freq = 880, dur = 0.15) {
                    try {
                        const Ctx = window.AudioContext || window.webkitAudioContext;
                        if (!Ctx) return;
                        const ctx = new Ctx(), osc = ctx.createOscillator(), g = ctx.createGain();
                        osc.connect(g); g.connect(ctx.destination);
                        osc.frequency.value = freq; osc.type = 'sine';
                        g.gain.setValueAtTime(0.25, ctx.currentTime);
                        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + dur);
                        osc.start(); osc.stop(ctx.currentTime + dur);
                    } catch(e) {}
                },

                openScanner() {
                    this.isScannerOpen = true;
                    this.$nextTick(() => this.startCamera());
                },

                startCamera() {
                    if (typeof Html5Qrcode === 'undefined') {
                        alert('Library scanner belum siap, coba lagi.');
                        return;
                    }
                    this.html5QrCode = new Html5Qrcode("reader");
                    this.html5QrCode.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: { width: 200, height: 200 }, aspectRatio: 1.0 },
                        (decoded) => {
                            navigator.vibrate && navigator.vibrate(100);
                            this.beep(880, 0.15);
                            @this.scanBarcodeDirect(decoded);
                        },
                        () => {}
                    ).catch(err => {
                        console.error("Gagal kamera:", err);
                        alert("Tidak dapat membuka kamera: " + (err.message || err));
                        this.isScannerOpen = false;
                    });
                },

                closeScanner() {
                    if (this.html5QrCode) {
                        this.html5QrCode.stop()
                            .then(() => { this.html5QrCode.clear(); this.html5QrCode = null; })
                            .catch(e => console.error(e));
                    }
                    this.isScannerOpen = false;
                }
            };
        }
    </script>
</x-filament-panels::page>
