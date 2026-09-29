<div
    x-data="posApp()"
    class="pos-app"
    style="height:100%;display:flex;flex-direction:column;"
    @keydown.window.f8.prevent="$wire.openCheckoutModal()"
    @keydown.window.escape.prevent="$wire.closeCheckoutModal()"
>

{{-- HTML5 QR scanner library (loaded async, tidak blocking) --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" async></script>

<style>
    /* ─────────────────────── POS Variables ─────────────────────── */
    .pos-app {
        --navy:   #1B2A4A;
        --navy-2: #162240;
        --navy-3: #0F1D36;
        --navy-4: #0C1829;
        --border: #243358;
        --blue:   #1E6BFA;
        --blue-h: #3B82F6;
        --gold:   #F59E0B;
        --red:    #EF4444;
        --muted:  #7E92B2;
        --text:   #EAF0FB;
        background: var(--navy-4);
        color: var(--text);
    }

    /* ─── SVG sizing ─── */
    .pos-app svg {
        display: inline-block !important;
        vertical-align: middle !important;
        flex-shrink: 0 !important;
        max-width: 100% !important;
        max-height: 100% !important;
    }

    /* ─── Shell ─── */
    .pos-shell { display: flex; flex-direction: column; flex: 1; overflow: hidden; }

    .pos-right {
        width: 100%;
        display: flex;
        flex-direction: column;
        background: var(--navy-3);
        border-left: 1px solid var(--border);
    }

    /* ─── Product grid ─── */
    .pos-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        padding: 10px;
    }

    /* ─── Product card ─── */
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
    .pos-card:hover  { border-color: var(--blue); }
    .pos-card:active { transform: scale(0.97); }
    .pos-card-img { flex: 1; width: 100%; object-fit: cover; display: block; }
    .pos-card-placeholder {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--navy-2), var(--navy-3));
        color: var(--border);
    }
    .pos-card-footer {
        padding: 6px 8px;
        background: var(--navy-2);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 4px;
        flex-shrink: 0;
    }
    .pos-card-name  { font-size: 11px; font-weight: 700; color: var(--text); line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .pos-card-price { font-size: 11px; font-weight: 600; color: var(--muted); margin-top: 1px; }
    .pos-card-add {
        width: 24px; height: 24px;
        border-radius: 6px;
        background: var(--blue);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; font-weight: 900; line-height: 1;
        flex-shrink: 0;
        border: none; cursor: pointer;
        transition: background .12s, transform .1s;
    }
    .pos-card-add:hover  { background: var(--blue-h); }
    .pos-card-add:active { transform: scale(0.88); }

    /* ─── Category tiles ─── */
    .cat-tile { display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; flex-shrink: 0; transition: transform .12s; background: none; border: none; }
    .cat-tile:active { transform: scale(0.92); }
    .cat-tile-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .cat-tile-label { font-size: 10px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); }
    .cat-tile.active .cat-tile-label { color: #fff; }

    /* ─── Cart items ─── */
    .cart-item { display: flex; align-items: flex-start; gap: 8px; padding: 8px; border-bottom: 1px solid var(--border); }
    .cart-thumb { width: 44px; height: 44px; border-radius: 8px; flex-shrink: 0; background: var(--navy-2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .stepper-btn { width: 22px; height: 22px; border-radius: 5px; background: var(--navy-2); border: 1px solid var(--border); color: var(--text); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 900; cursor: pointer; transition: background .1s; }
    .stepper-btn:hover { background: var(--border); }

    /* ─── Scrollbar ─── */
    .pos-scroll::-webkit-scrollbar { width: 4px; }
    .pos-scroll::-webkit-scrollbar-track { background: transparent; }
    .pos-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

    /* ─── Scanner laser ─── */
    @keyframes pos-laser { 0%{top:10%;opacity:.6} 50%{top:85%;opacity:1} 100%{top:10%;opacity:.6} }
    .pos-scan-line { animation: pos-laser 2s infinite ease-in-out; }

    /* ─── Print ─── */
    @media print {
        body * { visibility: hidden !important; }
        #pos-receipt, #pos-receipt * { visibility: visible !important; }
        #pos-receipt { position: fixed !important; left: 0 !important; top: 0 !important; width: 80mm !important; font-family: 'Courier New', monospace !important; font-size: 12px !important; color: #000 !important; background: #fff !important; }
        .no-print { display: none !important; }
    }

    /* ─── Responsive: tablet (1024px+) ─── */
    @media (min-width: 1024px) {
        .pos-shell       { flex-direction: row; }
        .pos-right       { width: 340px; min-width: 320px; max-width: 360px; }
        .pos-grid        { grid-template-columns: repeat(4, 1fr); gap: 8px; padding: 10px; }
        .mob-tabs        { display: none !important; }
    }

    @media (min-width: 1280px) {
        .pos-right { width: 360px; }
        .pos-grid  { gap: 10px; padding: 12px; }
    }

    @media (min-width: 640px) and (max-width: 1023px) {
        .pos-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 12px; }
    }
</style>

<!-- ═══════════════════════════ TOP BAR ═══════════════════════════ -->
<div style="background:var(--navy-3);border-bottom:1px solid var(--border);padding:0 12px;display:flex;align-items:center;gap:8px;flex-shrink:0;height:52px;">

    @if(auth()->user()?->hasAnyRole(['admin','super_admin']))
    <a href="/dasbor" title="Dashboard Admin"
       style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:var(--navy-2);border:1px solid var(--border);border-radius:8px;color:var(--muted);text-decoration:none;flex-shrink:0;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
    </a>
    @endif

    <!-- Brand -->
    <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
        <span style="font-size:17px;font-weight:900;color:#fff;letter-spacing:.02em;">SARINAH STREET</span>
        <span style="font-size:9px;font-weight:800;padding:2px 7px;border-radius:99px;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);color:#34D399;">POS</span>
    </div>

    <!-- Search -->
    <div style="flex:1;max-width:340px;margin:0 8px;position:relative;">
        <div style="position:absolute;top:50%;left:9px;transform:translateY(-50%);pointer-events:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7E92B2" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        </div>
        <input
            id="pos-search"
            type="text"
            wire:model.live.debounce.200ms="search"
            placeholder="Cari produk..."
            autocomplete="off"
            style="width:100%;height:34px;padding:0 32px 0 30px;font-size:12px;background:var(--navy-2);border:1px solid var(--border);border-radius:8px;color:#fff;outline:none;"
        />
        @if($search)
            <button wire:click="$set('search','')"
                    style="position:absolute;top:50%;right:8px;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;font-size:16px;line-height:1;">✕</button>
        @endif
    </div>

    <!-- Barcode form (hidden on mobile, visible on desktop) -->
    <form wire:submit.prevent="scanBarcode" style="display:flex;gap:6px;align-items:center;flex-shrink:0;">
        <input
            id="pos-barcode"
            type="text"
            wire:model="barcode"
            placeholder="Scan barcode..."
            autocomplete="off"
            style="width:110px;height:34px;padding:0 10px;font-size:12px;background:var(--navy-2);border:1px solid var(--border);border-radius:8px;color:#fff;outline:none;"
        />
        <button type="submit"
                style="height:34px;padding:0 12px;background:var(--blue);border-radius:8px;color:#fff;font-size:12px;font-weight:700;border:none;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:5px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2"/>
                <rect x="7" y="7" width="10" height="10" rx="1"/>
            </svg>
            Scan
        </button>
    </form>

    <!-- Kasir badge -->
    <div style="display:flex;align-items:center;gap:5px;flex-shrink:0;background:var(--navy-2);border:1px solid var(--border);height:34px;padding:0 10px;border-radius:8px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#7E92B2" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        <span style="font-size:11px;color:var(--muted);">Kasir:</span>
        <span style="font-size:12px;font-weight:800;color:#fff;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()?->name ?? 'Kasir' }}</span>
    </div>

    <!-- Logout -->
    <form method="POST" action="{{ route('filament.admin.auth.logout') }}" style="flex-shrink:0;margin:0;">
        @csrf
        <button type="submit" title="Keluar"
                style="height:34px;padding:0 12px;background:var(--navy-2);border:1px solid var(--border);border-radius:8px;color:var(--muted);font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:5px;white-space:nowrap;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            Keluar
        </button>
    </form>
</div>

<!-- ═══════════════════════════ MAIN SHELL ═══════════════════════════ -->
<div class="pos-shell" style="flex:1;overflow:hidden;">

    <!-- ─── LEFT: CATALOG ─── -->
    <div style="flex:1;display:flex;flex-direction:column;overflow:hidden;" class="{{ $mobileView === 'catalog' ? '' : 'hidden lg:flex' }}">

        <!-- Category Tiles Row -->
        <div class="pos-scroll" style="display:flex;gap:8px;padding:8px 12px;overflow-x:auto;flex-shrink:0;border-bottom:1px solid var(--border);align-items:center;">

            <!-- ALL tile -->
            <button type="button" wire:click="$set('selectedCategory', null)" class="cat-tile {{ is_null($selectedCategory) ? 'active' : '' }}">
                <div class="cat-tile-icon" style="background:{{ is_null($selectedCategory) ? 'linear-gradient(135deg,#3B82F6,#1D4ED8)' : 'var(--navy-2)' }};border:{{ is_null($selectedCategory) ? '2px solid #60A5FA' : '1px solid var(--border)' }};">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
                        <rect x="3"  y="3"  width="9" height="9" rx="2" fill="#EF4444"/>
                        <rect x="14" y="3"  width="9" height="9" rx="2" fill="#22C55E"/>
                        <rect x="3"  y="14" width="9" height="9" rx="2" fill="#3B82F6"/>
                        <rect x="14" y="14" width="9" height="9" rx="2" fill="#F59E0B"/>
                    </svg>
                </div>
                <span class="cat-tile-label" style="{{ is_null($selectedCategory) ? 'color:#fff' : '' }}">ALL</span>
            </button>

            @foreach($this->categories as $cat)
                @php
                    $cl = strtolower($cat->name);
                    if      (str_contains($cl,'kopi')||str_contains($cl,'coffee')||str_contains($cl,'minum')||str_contains($cl,'drink')) { $tg='linear-gradient(135deg,#F97316,#EA580C)';$tb='#FB923C';$ti='coffee'; }
                    elseif  (str_contains($cl,'teh')||str_contains($cl,'tea'))                                                           { $tg='linear-gradient(135deg,#16A34A,#15803D)';$tb='#4ADE80';$ti='tea';    }
                    elseif  (str_contains($cl,'makan')||str_contains($cl,'food')||str_contains($cl,'snack'))                             { $tg='linear-gradient(135deg,#DC2626,#B91C1C)';$tb='#F87171';$ti='food';   }
                    elseif  (str_contains($cl,'merch')||str_contains($cl,'baju')||str_contains($cl,'kaos'))                             { $tg='linear-gradient(135deg,#7C3AED,#6D28D9)';$tb='#A78BFA';$ti='shirt';  }
                    elseif  (str_contains($cl,'elektronik')||str_contains($cl,'gadget'))                                                { $tg='linear-gradient(135deg,#2563EB,#1D4ED8)';$tb='#60A5FA';$ti='bolt';   }
                    else                                                                                                                 { $tg='linear-gradient(135deg,#0891B2,#0E7490)';$tb='#22D3EE';$ti='tag';    }
                    $ia = $selectedCategory === $cat->id;
                @endphp
                <button type="button" wire:click="$set('selectedCategory', {{ $cat->id }})" class="cat-tile {{ $ia ? 'active' : '' }}" wire:key="cat-{{ $cat->id }}">
                    <div class="cat-tile-icon" style="background:{{ $ia ? $tg : 'var(--navy-2)' }};border:{{ $ia ? '2px solid '.$tb : '1px solid var(--border)' }};">
                        @if($ti==='coffee')
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                <path d="M17 8H19C20.1 8 21 8.9 21 10V11C21 12.1 20.1 13 19 13H17" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="2" stroke-linecap="round"/>
                                <path d="M3 8H17V15C17 16.1 16.1 17 15 17H5C3.9 17 3 16.1 3 15V8Z" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="2"/>
                                <path d="M6 5C6 5 6.5 6 6 7" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M10 5C10 5 10.5 6 10 7" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        @elseif($ti==='tea')
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                <path d="M4 9H16V17C16 18.1 15.1 19 14 19H6C4.9 19 4 18.1 4 17V9Z" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="2"/>
                                <path d="M16 11H18C19.1 11 20 11.9 20 13V13C20 14.1 19.1 15 18 15H16" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="2" stroke-linecap="round"/>
                                <path d="M9 5L9 9M7 7H11" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        @elseif($ti==='food')
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2C9.24 2 7 4.24 7 7V9H17V7C17 4.24 14.76 2 12 2Z" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="2"/>
                                <rect x="5" y="9" width="14" height="2" rx="1" fill="{{ $ia?'#FFF':'#7E92B2' }}"/>
                                <path d="M6 11H18L17 20H7L6 11Z" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="2" fill="none"/>
                            </svg>
                        @elseif($ti==='shirt')
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                <path d="M3 7L8 4L9.5 6.5C10.1 7.4 11 8 12 8C13 8 13.9 7.4 14.5 6.5L16 4L21 7L19 10L17 9V20H7V9L5 10L3 7Z" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        @elseif($ti==='bolt')
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="{{ $ia?'#FFF':'#7E92B2' }}">
                                <path d="M13 3L4 14H11L11 21L20 10L13 10Z"/>
                            </svg>
                        @else
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                <path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6Z" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M3 6H21" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="2"/>
                                <path d="M16 10C16 12.2 14.2 14 12 14C9.8 14 8 12.2 8 10" stroke="{{ $ia?'#FFF':'#7E92B2' }}" stroke-width="2"/>
                            </svg>
                        @endif
                    </div>
                    <span class="cat-tile-label" style="{{ $ia ? 'color:#fff' : '' }}">{{ strtoupper($cat->name) }}</span>
                </button>
            @endforeach

            @if($selectedCategory !== null || !empty($search))
                <button type="button" wire:click="resetFilters"
                        style="margin-left:auto;flex-shrink:0;font-size:10px;font-weight:700;padding:4px 10px;border-radius:6px;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;cursor:pointer;white-space:nowrap;">
                    ✕ Reset
                </button>
            @endif
        </div>

        <!-- Mobile Tabs (hidden on desktop) -->
        <div class="mob-tabs" style="display:grid;grid-template-columns:1fr 1fr;gap:6px;padding:8px 10px;flex-shrink:0;">
            <button type="button" wire:click="$set('mobileView','catalog')"
                    style="padding:8px;border-radius:8px;font-size:11px;font-weight:800;background:{{ $mobileView==='catalog'?'#1E6BFA':'#162240' }};color:{{ $mobileView==='catalog'?'#fff':'#7E92B2' }};border:1px solid #243358;cursor:pointer;">
                Katalog ({{ count($this->availableProducts) }})
            </button>
            <button type="button" wire:click="$set('mobileView','cart')"
                    style="padding:8px;border-radius:8px;font-size:11px;font-weight:800;background:{{ $mobileView==='cart'?'#1E6BFA':'#162240' }};color:{{ $mobileView==='cart'?'#fff':'#7E92B2' }};border:1px solid #243358;cursor:pointer;position:relative;">
                Order #{{ $orderNumber }}
                @if($this->totalItemCount > 0)
                    <span style="margin-left:4px;background:#EF4444;color:#fff;font-size:10px;font-weight:800;border-radius:99px;padding:0 5px;">{{ $this->totalItemCount }}</span>
                @endif
            </button>
        </div>

        <!-- ── Product Grid ── -->
        <div class="pos-scroll" style="flex:1;overflow-y:auto;overflow-x:hidden;">
            <div class="pos-grid">
                @forelse ($this->availableProducts as $product)
                    @php
                        $pk = $product->selling_price >= 1000
                            ? 'Rp ' . rtrim(rtrim(number_format($product->selling_price / 1000, 1, '.', ''), '0'), '.') . 'k'
                            : 'Rp ' . number_format($product->selling_price, 0, ',', '.');
                    @endphp
                    <div class="pos-card" wire:click="addToCart({{ $product->id }})" wire:key="prod-{{ $product->id }}">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="pos-card-img" loading="lazy" />
                        @else
                            <div class="pos-card-placeholder">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity=".4">
                                    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8L6 7h12z"/>
                                    <circle cx="12" cy="14" r="3"/>
                                </svg>
                            </div>
                        @endif
                        <div class="pos-card-footer">
                            <div style="min-width:0;flex:1;">
                                <div class="pos-card-name">{{ $product->name }}</div>
                                <div class="pos-card-price">{{ $pk }}</div>
                            </div>
                            <button type="button" wire:click.stop="addToCart({{ $product->id }})" class="pos-card-add" title="Tambah ke order">+</button>
                        </div>
                    </div>
                @empty
                    <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--muted);">
                        <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">Tidak ada produk</div>
                        <div style="font-size:12px;">Coba ubah pencarian atau pilih kategori lain</div>
                        @if($selectedCategory !== null || !empty($search))
                            <button type="button" wire:click="resetFilters"
                                    style="margin-top:14px;padding:7px 18px;background:var(--blue);border-radius:8px;color:#fff;font-size:12px;font-weight:700;border:none;cursor:pointer;">
                                Tampilkan Semua
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>{{-- /pos-left --}}

    <!-- ─── RIGHT: ORDER TICKET ─── -->
    <div class="pos-right {{ $mobileView === 'cart' ? '' : 'hidden lg:flex' }}" style="flex-direction:column;">

        <!-- Order Header -->
        <div style="padding:8px 12px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-shrink:0;gap:8px;">
            <div style="flex-shrink:0;">
                <span style="font-size:14px;font-weight:800;color:#fff;">ORDER #{{ $orderNumber }}</span>
                @if($this->totalItemCount > 0)
                    <span style="margin-left:5px;font-size:11px;color:var(--muted);">({{ $this->totalItemCount }} item)</span>
                @endif
            </div>
            <input type="text" wire:model.blur="customer_name" placeholder="Nama pelanggan..."
                   style="flex:1;min-width:0;padding:5px 8px;font-size:11px;background:var(--navy-2);border:1px solid var(--border);border-radius:6px;color:#fff;outline:none;" />
        </div>

        <!-- Cart Items -->
        <div class="pos-scroll" style="flex:1;overflow-y:auto;">
            @forelse($cart as $id => $item)
                <div class="cart-item" wire:key="cart-{{ $id }}">
                    <!-- Thumbnail -->
                    <div class="cart-thumb">
                        @if(!empty($item['image']))
                            <img src="{{ asset('storage/' . $item['image']) }}" alt="" style="width:100%;height:100%;object-fit:cover;" />
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7E92B2" stroke-width="1.5">
                                <rect x="2" y="7" width="20" height="14" rx="2"/><circle cx="12" cy="14" r="3"/>
                            </svg>
                        @endif
                    </div>

                    <!-- Info -->
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:700;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $item['name'] }}
                        </div>
                        <div style="font-size:11px;color:var(--muted);font-weight:600;margin-top:1px;">
                            {{ $item['quantity'] }}x &nbsp;Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                        </div>
                    </div>

                    <!-- Stepper + Delete -->
                    <div style="display:flex;align-items:center;gap:3px;flex-shrink:0;">
                        <button type="button" wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})" class="stepper-btn">-</button>
                        <span style="min-width:18px;text-align:center;font-size:12px;font-weight:800;color:var(--text);">{{ $item['quantity'] }}</span>
                        <button type="button" wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})" class="stepper-btn">+</button>
                        <button type="button" wire:click="removeFromCart({{ $id }})"
                                style="width:22px;height:22px;border-radius:5px;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.25);color:#F87171;cursor:pointer;display:flex;align-items:center;justify-content:center;margin-left:2px;font-size:13px;font-weight:900;line-height:1;">
                            ✕
                        </button>
                    </div>
                </div>
            @empty
                <div style="padding:40px 16px;text-align:center;color:var(--muted);">
                    <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:6px;">Keranjang kosong</div>
                    <div style="font-size:11px;">Pilih produk atau scan barcode</div>
                </div>
            @endforelse
        </div>

        <!-- Summary + Actions (pinned bottom) -->
        <div style="padding:10px 12px;border-top:1px solid var(--border);flex-shrink:0;">

            <!-- Subtotal -->
            <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-bottom:4px;">
                <span>Subtotal</span>
                <span style="font-weight:600;color:var(--text);">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
            </div>

            <!-- Diskon -->
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:var(--muted);margin-bottom:4px;">
                <span>Diskon (Rp)</span>
                <input type="number" wire:model.live.debounce.300ms="discount" min="0" placeholder="0"
                       style="width:90px;text-align:right;padding:3px 6px;font-size:11px;font-weight:700;background:var(--navy-2);border:1px solid var(--border);border-radius:6px;color:#fff;outline:none;" />
            </div>

            <!-- Total -->
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-top:1px solid var(--border);margin-bottom:10px;">
                <span style="font-size:14px;font-weight:800;color:#fff;">Total</span>
                <span style="font-size:17px;font-weight:900;color:#fff;letter-spacing:-.02em;">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
            </div>

            <!-- CLEAR + Scan Kamera HP -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                <button type="button"
                        wire:click="clearCart"
                        @if(empty($cart)) disabled @endif
                        wire:confirm="{{ empty($cart) ? '' : 'Kosongkan seluruh order?' }}"
                        style="padding:10px;border-radius:10px;background:{{ empty($cart) ? 'rgba(239,68,68,.3)' : '#EF4444' }};color:#fff;font-size:13px;font-weight:800;border:none;cursor:{{ empty($cart) ? 'not-allowed' : 'pointer' }};opacity:{{ empty($cart) ? '.4' : '1' }};">
                    CLEAR
                </button>

                <button type="button"
                        @click="openScanner()"
                        style="padding:10px;border-radius:10px;background:var(--gold);color:#111;font-size:12px;font-weight:900;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    <span>Scan Kamera HP</span>
                </button>
            </div>

            <!-- BAYAR (F8) -->
            <button type="button"
                    wire:click="openCheckoutModal"
                    @if(empty($cart)) disabled @endif
                    style="width:100%;padding:13px;border-radius:10px;background:{{ empty($cart) ? '#1E3A6E' : '#1E6BFA' }};color:#fff;font-size:14px;font-weight:900;border:none;cursor:{{ empty($cart) ? 'not-allowed' : 'pointer' }};opacity:{{ empty($cart) ? '.5' : '1' }};letter-spacing:.01em;">
                BAYAR &nbsp;Rp {{ number_format($this->total, 0, ',', '.') }} &nbsp;(F8)
            </button>
        </div>
    </div>{{-- /pos-right --}}

</div>{{-- /pos-shell --}}

{{-- ═══════════════════════════ CHECKOUT MODAL ═══════════════════════════ --}}
@if($isCheckoutModalOpen)
    <div style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.75);backdrop-filter:blur(4px);">
        <div style="background:var(--navy-3);border:1px solid var(--border);border-radius:20px;max-width:480px;width:100%;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.6);display:flex;flex-direction:column;">

            <div style="padding:14px 18px;background:var(--navy-4);border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:15px;font-weight:800;color:#fff;">Checkout Order #{{ $orderNumber }}</div>
                    <div style="font-size:11px;color:var(--muted);">{{ $this->totalItemCount }} item &bull; Konfirmasi pembayaran</div>
                </div>
                <button wire:click="closeCheckoutModal" style="width:30px;height:30px;display:flex;align-items:center;justify-content:center;background:var(--navy-2);border:1px solid var(--border);border-radius:8px;color:var(--muted);cursor:pointer;font-size:18px;line-height:1;">✕</button>
            </div>

            <div class="pos-scroll" style="padding:16px 18px;max-height:70vh;overflow-y:auto;display:flex;flex-direction:column;gap:14px;">

                <!-- Total card -->
                <div style="background:linear-gradient(135deg,rgba(30,107,250,.2),rgba(29,78,216,.12));border:1px solid rgba(30,107,250,.35);border-radius:14px;padding:16px;text-align:center;">
                    <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#93C5FD;">Total Tagihan</div>
                    <div style="font-size:30px;font-weight:900;color:#fff;letter-spacing:-.02em;margin-top:4px;">Rp {{ number_format($this->total, 0, ',', '.') }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $this->totalItemCount }} item pesanan</div>
                </div>

                <!-- Nama pelanggan -->
                <div>
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:6px;letter-spacing:.05em;">Nama Pelanggan</div>
                    <input type="text" wire:model="customer_name" placeholder="Pelanggan Umum"
                           style="width:100%;padding:9px 12px;font-size:13px;background:var(--navy-2);border:1px solid var(--border);border-radius:10px;color:#fff;outline:none;" />
                </div>

                <!-- Metode bayar -->
                <div>
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:8px;letter-spacing:.05em;">Metode Pembayaran</div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                        @foreach(['cash'=>'💵 Tunai','qris'=>'📱 QRIS','transfer'=>'🏦 Transfer'] as $m=>$l)
                            <button type="button" wire:click="$set('payment_method','{{ $m }}')"
                                    style="padding:10px 6px;border-radius:10px;font-size:12px;font-weight:800;border:{{ $payment_method===$m?'2px solid #60A5FA':'1px solid var(--border)' }};background:{{ $payment_method===$m?'var(--blue)':'var(--navy-2)' }};color:{{ $payment_method===$m?'#fff':'var(--muted)' }};cursor:pointer;transition:.12s;">
                                {{ $l }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Cash panel -->
                @if($payment_method==='cash')
                    <div style="background:var(--navy-4);border:1px solid var(--border);border-radius:12px;padding:12px;display:flex;flex-direction:column;gap:10px;">
                        @if(!empty($this->cashSuggestions))
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                @foreach($this->cashSuggestions as $s)
                                    <button type="button" wire:click="setPaidAmount({{ $s }})"
                                            style="padding:5px 10px;font-size:11px;font-weight:700;border-radius:6px;background:var(--navy-2);border:1px solid var(--border);color:#93C5FD;cursor:pointer;">
                                        {{ $s == $this->total ? 'Uang Pas' : 'Rp ' . number_format($s, 0, ',', '.') }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;gap:10px;">
                            <span style="color:var(--muted);white-space:nowrap;">Uang Diterima:</span>
                            <div style="position:relative;width:150px;flex-shrink:0;">
                                <span style="position:absolute;top:50%;left:8px;transform:translateY(-50%);color:var(--muted);font-size:11px;font-weight:700;">Rp</span>
                                <input type="number" wire:model.live.debounce.200ms="paid_amount" min="0"
                                       style="width:100%;text-align:right;padding:8px 8px 8px 26px;font-size:13px;font-weight:800;background:var(--navy-2);border:1px solid var(--border);border-radius:8px;color:#fff;outline:none;" />
                            </div>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding-top:8px;border-top:1px solid var(--border);">
                            <span style="font-size:12px;color:var(--muted);">Kembalian:</span>
                            <span style="font-size:17px;font-weight:900;color:{{ $this->changeAmount >= 0 ? '#34D399' : '#F87171' }};">
                                Rp {{ number_format(max(0, $this->changeAmount), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <div style="padding:12px 18px;background:var(--navy-4);border-top:1px solid var(--border);display:flex;gap:8px;">
                <button wire:click="closeCheckoutModal"
                        style="flex:1;padding:11px;border-radius:10px;background:var(--navy-2);border:1px solid var(--border);color:var(--muted);font-size:13px;font-weight:700;cursor:pointer;">
                    Batal
                </button>
                <button wire:click="checkout"
                        style="flex:2;padding:11px;border-radius:10px;background:var(--blue);color:#fff;font-size:13px;font-weight:900;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(30,107,250,.3);">
                    ✓ Konfirmasi &amp; Bayar
                </button>
            </div>
        </div>
    </div>
@endif

{{-- ═══════════════════════════ CAMERA SCANNER ═══════════════════════════ --}}
<div x-show="isScannerOpen" x-cloak
     style="position:fixed;inset:0;z-index:60;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.88);backdrop-filter:blur(6px);">
    <div @click.outside="closeScanner()"
         style="background:var(--navy-3);border:1px solid var(--border);border-radius:20px;max-width:340px;width:100%;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.7);display:flex;flex-direction:column;">
        <div style="padding:12px 16px;background:var(--navy-4);border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:14px;font-weight:800;color:#fff;">📷 Scan Kamera HP</span>
            <button @click="closeScanner()" style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;background:var(--navy-2);border:1px solid var(--border);border-radius:8px;color:var(--muted);cursor:pointer;font-size:16px;">✕</button>
        </div>
        <div style="padding:14px;display:flex;flex-direction:column;gap:10px;">
            <p style="font-size:11px;color:var(--muted);text-align:center;line-height:1.4;">Arahkan kamera ke barcode produk.<br>Akan otomatis masuk ke keranjang.</p>
            <div style="position:relative;width:100%;border-radius:12px;overflow:hidden;background:#000;" x-ref="readerWrap">
                <div id="reader" style="width:100%;min-height:220px;"></div>
                <div x-show="isScannerOpen" style="position:absolute;inset:20px;border:2px solid rgba(52,211,153,.6);border-radius:10px;pointer-events:none;">
                    <div class="pos-scan-line" style="position:absolute;left:0;right:0;height:2px;background:linear-gradient(to right,transparent,#34D399,transparent);box-shadow:0 0 10px #34D399;"></div>
                </div>
            </div>
            <button @click="closeScanner()"
                    style="padding:10px;border-radius:10px;background:var(--navy-2);border:1px solid var(--border);color:var(--text);font-size:12px;font-weight:700;cursor:pointer;">
                Tutup Kamera
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════ RECEIPT MODAL ═══════════════════════════ --}}
@if($last_sale)
    <div style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.75);backdrop-filter:blur(4px);">
        <div style="background:#fff;border-radius:20px;max-width:360px;width:100%;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.6);display:flex;flex-direction:column;color:#111;">
            <div class="no-print" style="padding:14px 16px;background:#059669;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:14px;font-weight:800;color:#fff;">✓ Transaksi Berhasil!</span>
                <button wire:click="$set('last_sale', null)" style="background:none;border:none;color:#A7F3D0;cursor:pointer;font-size:20px;line-height:1;">✕</button>
            </div>
            <div class="pos-scroll" style="padding:16px;max-height:65vh;overflow-y:auto;font-family:'Courier New',monospace;font-size:12px;" id="pos-receipt">
                <div style="text-align:center;padding-bottom:10px;border-bottom:1px dashed #ccc;">
                    <div style="font-size:15px;font-weight:900;letter-spacing:.08em;">SARINAH STREET</div>
                    <div style="color:#555;margin-top:2px;">{{ $last_sale['invoice_number'] }}</div>
                    <div style="color:#999;font-size:11px;margin-top:1px;">{{ $last_sale['time'] }} &bull; {{ strtoupper($last_sale['payment_method']) }}</div>
                </div>
                <div style="padding:10px 0;border-bottom:1px dashed #ccc;">
                    @foreach($last_sale['items'] as $item)
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;gap:8px;">
                            <div>
                                <div style="font-weight:700;">{{ $item['name'] }}</div>
                                <div style="color:#888;font-size:11px;">{{ $item['qty'] }} pcs</div>
                            </div>
                            <div style="font-weight:700;white-space:nowrap;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
                <div style="padding:10px 0;border-bottom:1px dashed #ccc;">
                    <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:900;"><span>TOTAL</span><span>Rp {{ number_format($last_sale['total'], 0, ',', '.') }}</span></div>
                    <div style="display:flex;justify-content:space-between;color:#666;margin-top:4px;"><span>Dibayar</span><span>Rp {{ number_format($last_sale['paid_amount'], 0, ',', '.') }}</span></div>
                    <div style="display:flex;justify-content:space-between;color:#059669;font-weight:700;"><span>Kembalian</span><span>Rp {{ number_format($last_sale['change_amount'], 0, ',', '.') }}</span></div>
                </div>
                <div style="text-align:center;padding-top:10px;color:#bbb;font-size:11px;">Terima kasih sudah berbelanja!</div>
            </div>
            <div class="no-print" style="padding:12px 16px;background:#F9FAFB;border-top:1px solid #E5E7EB;display:flex;gap:8px;">
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

</div>{{-- /pos-app --}}

<script>
function posApp() {
    return {
        isScannerOpen : false,
        html5QrCode   : null,

        init() {
            // Shortcut '/' → fokus search
            window.addEventListener('keydown', (e) => {
                if (e.key === '/' && !['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) {
                    e.preventDefault();
                    document.getElementById('pos-search')?.focus();
                }
            });

            // Audio events dari Livewire
            this.$el.addEventListener('play-beep',          () => this.beep(880, 0.12));
            this.$el.addEventListener('play-error-beep',    () => this.beep(330, 0.25));
            this.$el.addEventListener('play-success-sound', () => {
                this.beep(523, 0.1);
                setTimeout(() => this.beep(659, 0.15), 120);
            });
        },

        beep(freq = 880, dur = 0.15) {
            try {
                const Ctx = window.AudioContext || window.webkitAudioContext;
                if (!Ctx) return;
                const ctx = new Ctx();
                const osc = ctx.createOscillator();
                const g   = ctx.createGain();
                osc.connect(g);
                g.connect(ctx.destination);
                osc.frequency.value = freq;
                osc.type = 'sine';
                g.gain.setValueAtTime(0.2, ctx.currentTime);
                g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + dur);
                osc.start();
                osc.stop(ctx.currentTime + dur);
            } catch (_) {}
        },

        openScanner() {
            this.isScannerOpen = true;
            // Tunggu DOM render dulu
            this.$nextTick(() => {
                // Pastikan library sudah loaded
                const tryStart = () => {
                    if (typeof Html5Qrcode !== 'undefined') {
                        this.startCamera();
                    } else {
                        setTimeout(tryStart, 200);
                    }
                };
                tryStart();
            });
        },

        startCamera() {
            // Bersihkan reader element dulu jika ada sisa
            const readerEl = document.getElementById('reader');
            if (readerEl) readerEl.innerHTML = '';

            this.html5QrCode = new Html5Qrcode('reader');
            this.html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 200, height: 200 } },
                (decoded) => {
                    navigator.vibrate && navigator.vibrate([100]);
                    this.beep(880, 0.15);
                    this.$wire.scanBarcodeDirect(decoded);
                    // Optional: tutup scanner otomatis setelah scan
                    // this.closeScanner();
                },
                () => {} // frame error handler (aman di-ignore)
            ).catch((err) => {
                console.error('Kamera error:', err);
                alert('Tidak dapat membuka kamera: ' + (err.message ?? err));
                this.isScannerOpen = false;
            });
        },

        closeScanner() {
            if (this.html5QrCode) {
                this.html5QrCode.stop()
                    .then(() => {
                        this.html5QrCode.clear();
                        this.html5QrCode = null;
                    })
                    .catch(() => {
                        this.html5QrCode = null;
                    });
            }
            this.isScannerOpen = false;
        }
    };
}
</script>
