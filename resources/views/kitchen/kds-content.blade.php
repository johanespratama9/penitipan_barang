{{-- ─────────────────────────────────────────────────────────────
     KITCHEN DISPLAY SYSTEM (KDS) CONTENT
     Digunakan bersama oleh Filament Page & Standalone Route /dapur
───────────────────────────────────────────────────────────── --}}
@php
    $orders = $orders ?? $this->orders;
    $metrics = $metrics ?? $this->metrics;
@endphp
<div 
    class="kds-root" 
    x-data="kitchenDisplayApp()" 
    wire:poll.4s="checkNewOrders"
>

    <!-- ─────────────────────────────────────────────────────────────
         1. TOP BAR METRICS & CONTROLS
    ───────────────────────────────────────────────────────────── -->
    <div class="kds-topbar no-print">
        
        <!-- Baris Atas: Judul & KPI Status -->
        <div class="kds-top-row">
            <div class="kds-brand-wrap">
                <div class="kds-brand-icon">🍳</div>
                <div>
                    <h1 class="kds-page-title">SISTEM ORDER DAPUR (KDS)</h1>
                    <div class="kds-subtitle">
                        <span class="kds-live-indicator"></span>
                        <span>Sinkronisasi Otomatis Realtime</span>
                        <span class="kds-time-clock" x-text="currentTime"></span>
                    </div>
                </div>
            </div>

            <!-- KPI Badges -->
            <div class="kds-metrics-bar">
                <div class="kds-metric-card metric-pending" wire:click="setFilter('pending')">
                    <span class="metric-label">Menunggu</span>
                    <span class="metric-value">{{ $metrics['pending'] ?? 0 }}</span>
                </div>
                <div class="kds-metric-card metric-cooking" wire:click="setFilter('cooking')">
                    <span class="metric-label">Dimasak</span>
                    <span class="metric-value">{{ $metrics['cooking'] ?? 0 }}</span>
                </div>
                <div class="kds-metric-card metric-ready" wire:click="setFilter('ready')">
                    <span class="metric-label">Siap Saji</span>
                    <span class="metric-value">{{ $metrics['ready'] ?? 0 }}</span>
                </div>
                <div class="kds-metric-card metric-completed" wire:click="setFilter('completed')">
                    <span class="metric-label">Full Selesai</span>
                    <span class="metric-value">{{ $metrics['completed'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Baris Bawah: Tabs Filter, Search, & Utility Buttons -->
        <div class="kds-control-row">
            
            <!-- Tabs Filter -->
            <div class="kds-tabs">
                <button 
                    type="button" 
                    wire:click="setFilter('active')" 
                    class="kds-tab {{ $filter === 'active' ? 'active' : '' }}"
                >
                    🔥 Pesanan Aktif ({{ $metrics['active'] ?? 0 }})
                </button>
                <button 
                    type="button" 
                    wire:click="setFilter('pending')" 
                    class="kds-tab tab-pending {{ $filter === 'pending' ? 'active' : '' }}"
                >
                    🟡 Menunggu ({{ $metrics['pending'] ?? 0 }})
                </button>
                <button 
                    type="button" 
                    wire:click="setFilter('cooking')" 
                    class="kds-tab tab-cooking {{ $filter === 'cooking' ? 'active' : '' }}"
                >
                    🍳 Dimasak ({{ $metrics['cooking'] ?? 0 }})
                </button>
                <button 
                    type="button" 
                    wire:click="setFilter('ready')" 
                    class="kds-tab tab-ready {{ $filter === 'ready' ? 'active' : '' }}"
                >
                    🛎️ Siap Saji ({{ $metrics['ready'] ?? 0 }})
                </button>
                <button 
                    type="button" 
                    wire:click="setFilter('completed')" 
                    class="kds-tab tab-completed {{ $filter === 'completed' ? 'active' : '' }}"
                >
                    ✅ Full Selesai ({{ $metrics['completed'] ?? 0 }})
                </button>
            </div>

            <!-- Search & Actions -->
            <div class="kds-actions-group">
                <div class="kds-search-box">
                    <svg class="kds-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari no meja / invoice..."
                        class="kds-search-input"
                    >
                </div>

                <!-- Sound Toggle Button -->
                <button 
                    type="button" 
                    wire:click="toggleSound" 
                    class="kds-tool-btn {{ $soundEnabled ? 'active-tool' : '' }}"
                    title="Aktifkan/Bisukan Suara Lonceng Pesanan Baru"
                >
                    @if($soundEnabled)
                        <svg class="kds-icon-tool text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span>Bel Aktif</span>
                    @else
                        <svg class="kds-icon-tool text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                        </svg>
                        <span>Bisu</span>
                    @endif
                </button>

                <!-- Shortcut Link POS Kasir (Hanya jika admin/kasir) -->
                @if(auth()->user()?->hasAnyRole(['admin', 'kasir', 'super_admin']))
                    <a href="{{ route('pos.terminal') }}" class="kds-tool-btn">
                        <svg class="kds-icon-tool" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Layar Kasir</span>
                    </a>
                @endif

                <!-- Identitas Pengguna -->
                <div class="kds-user-badge" style="display:flex;align-items:center;gap:6px;padding:6px 10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:8px;font-size:12px;color:#E2E8F0;">
                    <span style="font-size:13px;">👨‍🍳</span>
                    <span style="font-weight:700;">{{ auth()->user()?->name ?? 'Dapur' }}</span>
                </div>

                <!-- Tombol Logout Khusus Layar Dapur -->
                <form method="POST" action="{{ route('filament.admin.auth.logout') }}" style="display:inline;margin:0;">
                    @csrf
                    <button type="submit" class="kds-tool-btn" title="Keluar / Logout" style="color:#FCA5A5;border-color:rgba(239,68,68,.3);background:rgba(239,68,68,.1);">
                        <svg class="kds-icon-tool" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>

                <!-- Fullscreen Toggle -->
                <button type="button" @click="toggleFullscreen()" class="kds-tool-btn" title="Mode Layar Penuh">
                    <svg class="kds-icon-tool" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    <span x-text="isFullscreen ? 'Keluar Full' : 'Fullscreen'"></span>
                </button>
            </div>
        </div>

    </div>

    <!-- ─────────────────────────────────────────────────────────────
         2. KITCHEN ORDERS GRID
    ───────────────────────────────────────────────────────────── -->
    <div class="kds-board-container">
        @if($orders->isEmpty())
            <div class="kds-empty-state no-print">
                <div class="kds-empty-icon">✓</div>
                <h3 class="kds-empty-title">
                    @if($filter === 'completed')
                        Belum ada pesanan yang selesai hari ini.
                    @elseif($filter === 'pending')
                        Tidak ada antrean pesanan baru. Dapur siap menerima order!
                    @elseif($filter === 'cooking')
                        Tidak ada pesanan yang sedang dimasak saat ini.
                    @elseif($filter === 'ready')
                        Tidak ada makanan yang menunggu diantar ke meja.
                    @else
                        Dapur Santai! Tidak ada pesanan aktif saat ini.
                    @endif
                </h3>
                <p class="kds-empty-desc">
                    Setiap transaksi yang di-checkout kasir di POS otomatis muncul di layar ini.
                </p>
                <div class="kds-empty-actions">
                    <button type="button" wire:click="setFilter('active')" class="kds-btn-empty">
                        Tampilkan Semua Pesanan Aktif
                    </button>
                </div>
            </div>
        @else
            <div class="kds-cards-grid">
                @foreach($orders as $order)
                    @php
                        $status = $order->kitchen_status ?? 'pending';
                        $isCompleted = ($status === 'completed');
                        $isCooking = ($status === 'cooking');
                        $isReady = ($status === 'ready');
                        $isPending = ($status === 'pending');

                        // Hitung durasi sejak order masuk
                        $minutesAgo = $order->sold_at ? $order->sold_at->diffInMinutes(now()) : 0;
                        $isDelayed = ($minutesAgo >= 15 && !$isCompleted);

                        // Durasi selesai jika completed
                        $totalCookingDuration = null;
                        if ($order->kitchen_completed_at && $order->sold_at) {
                            $totalCookingDuration = $order->sold_at->diffInMinutes($order->kitchen_completed_at);
                        }
                    @endphp

                    <!-- ── SATU KARTU PESANAN DAPUR ── -->
                    <div class="kds-card status-{{ $status }} {{ $isDelayed ? 'card-delayed' : '' }}">
                        
                        <!-- Header Kartu -->
                        <div class="kds-card-header">
                            
                            <!-- Nomor Meja Besar & Mencolok -->
                            <div class="kds-card-table-row">
                                <div class="kds-table-badge">
                                    <span class="table-icon">📍</span>
                                    <span class="table-text">{{ $order->table_number ?: 'BUNGKUS / UMUM' }}</span>
                                </div>

                                <!-- Status Badge -->
                                <div class="kds-status-badge badge-{{ $status }}">
                                    @if($isPending)
                                        <span class="pulse-dot"></span>
                                        <span>ORDER MASUK</span>
                                    @elseif($isCooking)
                                        <span>🍳 DIMASAK</span>
                                    @elseif($isReady)
                                        <span>🛎️ SIAP SAJI</span>
                                    @elseif($isCompleted)
                                        <span>✅ ALL DONE</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Invoice & Waktu Masuk -->
                            <div class="kds-card-meta-row">
                                <div class="meta-inv font-mono">{{ $order->invoice_number }}</div>
                                <div class="meta-time {{ $isDelayed ? 'time-delayed' : '' }}">
                                    ⏱️ {{ $minutesAgo }}m yang lalu ({{ $order->sold_at ? $order->sold_at->format('H:i') : '-' }})
                                </div>
                            </div>

                            <!-- Info Kasir & Pelanggan -->
                            <div class="kds-card-customer-row">
                                <span class="customer-name">👤 {{ $order->customer_name ?: 'Pelanggan Umum' }}</span>
                                <span class="cashier-name">Kasir: {{ $order->cashier?->name ?: 'Kasir' }}</span>
                            </div>

                        </div>

                        <!-- Catatan Khusus Dapur (Penting) -->
                        @if(!empty($order->order_notes))
                            <div class="kds-order-notes">
                                <span class="notes-icon">⚠️</span>
                                <div class="notes-content">
                                    <div class="notes-title">Catatan Pesanan:</div>
                                    <div class="notes-text">{{ $order->order_notes }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Daftar Item Pesanan -->
                        <div class="kds-items-list">
                            <div class="kds-items-header">DAFTAR MENU PESANAN:</div>
                            @foreach($order->items as $item)
                                @php
                                    $isItemChecked = $this->checkedItems[$item->id] ?? $isCompleted;
                                @endphp
                                <div 
                                    class="kds-item-row {{ $isItemChecked ? 'item-checked' : '' }}"
                                    wire:click="toggleItemCheck({{ $item->id }})"
                                >
                                    <div class="kds-item-qty">{{ $item->quantity }}x</div>
                                    <div class="kds-item-info">
                                        <div class="kds-item-name">{{ $item->product_name }}</div>
                                        @if(!empty($item->notes))
                                            <div class="kds-item-note">Catatan: {{ $item->notes }}</div>
                                        @endif
                                    </div>
                                    <div class="kds-item-check-btn">
                                        @if($isItemChecked)
                                            <span class="check-icon-done">✓</span>
                                        @else
                                            <span class="check-icon-empty">○</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Info Cetak Bon Dapur -->
                        <div class="kds-print-status-row">
                            <div class="print-status-text">
                                @if($order->kitchen_printed_at)
                                    <span class="text-emerald-400">✓ Bon Terprint ({{ $order->kitchen_printed_at->format('H:i') }})</span>
                                @else
                                    <span class="text-slate-400">Belum Terprint</span>
                                @endif
                            </div>

                            <!-- Tombol Cetak Tiket Bon Dapur Langsung -->
                            <button 
                                type="button" 
                                wire:click="printKitchenTicket({{ $order->id }})" 
                                class="kds-btn-print-ticket no-print"
                                title="Cetak Bon Tiket Dapur (KOT) untuk Meja Ini"
                            >
                                <svg class="w-3.5 h-3.5 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak Tiket
                            </button>
                        </div>

                        <!-- ── ACTION BUTTONS: TAHAPAN STATUS PROSES DAPUR ── -->
                        <div class="kds-card-footer no-print">
                            
                            @if($isPending)
                                <!-- Step 1: Dari Menunggu -> Mulai Masak -->
                                <button 
                                    type="button" 
                                    wire:click="startCooking({{ $order->id }})" 
                                    class="kds-action-btn btn-start-cooking"
                                >
                                    <span>🔥 MULAI MASAK / PROSES</span>
                                </button>
                            @elseif($isCooking)
                                <!-- Step 2: Dari Dimasak -> Siap Saji -->
                                <button 
                                    type="button" 
                                    wire:click="markReady({{ $order->id }})" 
                                    class="kds-action-btn btn-mark-ready"
                                >
                                    <span>🛎️ TANDAI SIAP SAJI</span>
                                </button>
                            @elseif($isReady)
                                <!-- Step 3: Dari Siap Saji -> SEMUA SELESAI (ALL DONE) -->
                                <button 
                                    type="button" 
                                    wire:click="markCompleted({{ $order->id }})" 
                                    class="kds-action-btn btn-mark-completed"
                                >
                                    <span>✅ SELESAI SEMUA (ALL DONE)</span>
                                </button>
                            @elseif($isCompleted)
                                <!-- PENANDA KHUSUS: ORDERAN SUDAH FULL SELESAI -->
                                <div class="kds-full-completed-stamp">
                                    <div class="stamp-icon">✓</div>
                                    <div class="stamp-content">
                                        <div class="stamp-title">FULL SELESAI (ALL DONE)</div>
                                        <div class="stamp-sub">
                                            @if($totalCookingDuration !== null)
                                                Durasi: {{ $totalCookingDuration }} menit &bull; Selesai: {{ $order->kitchen_completed_at ? $order->kitchen_completed_at->format('H:i') : '-' }}
                                            @else
                                                Semua menu telah disajikan
                                            @endif
                                        </div>
                                    </div>
                                    <button 
                                        type="button" 
                                        wire:click="rollbackStatus({{ $order->id }}, 'cooking')" 
                                        class="stamp-undo-btn"
                                        title="Kembalikan status jika salah klik"
                                    >
                                        ↺
                                    </button>
                                </div>
                            @endif

                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ─────────────────────────────────────────────────────────────
         3. MODAL CETAK TIKET DAPUR (KITCHEN ORDER TICKET / KOT)
         Mendukung Thermal Printer 58mm / 80mm
    ───────────────────────────────────────────────────────────── -->
    @if($selectedTicketForPrint)
        <div class="kds-modal-backdrop">
            <div class="kds-modal-sheet">
                
                <!-- Modal Bar -->
                <div class="kds-modal-bar no-print">
                    <span class="font-bold text-slate-800">🖨️ Pratinjau Tiket Dapur (KOT)</span>
                    <button wire:click="closePrintTicketModal" class="kds-modal-close">✕</button>
                </div>

                <!-- Printable Paper Thermal View -->
                <div class="kds-thermal-paper" id="kitchen-ticket-print">
                    <div class="thermal-header">
                        <div class="thermal-title">DAPUR SARINAH STREET</div>
                        <div class="thermal-subtitle">=== TIKET PESANAN DAPUR ===</div>
                    </div>

                    <div class="thermal-divider"></div>

                    <div class="thermal-info-block">
                        <div class="thermal-table-highlight">
                            {{ $selectedTicketForPrint['table_number'] }}
                        </div>
                        <div class="thermal-row">
                            <span>No Order:</span>
                            <span class="font-bold">{{ $selectedTicketForPrint['invoice_number'] }}</span>
                        </div>
                        <div class="thermal-row">
                            <span>Jam Masuk:</span>
                            <span>{{ $selectedTicketForPrint['time'] }}</span>
                        </div>
                        <div class="thermal-row">
                            <span>Kasir:</span>
                            <span>{{ $selectedTicketForPrint['cashier_name'] }}</span>
                        </div>
                        <div class="thermal-row">
                            <span>Pelanggan:</span>
                            <span>{{ $selectedTicketForPrint['customer_name'] }}</span>
                        </div>
                    </div>

                    @if(!empty($selectedTicketForPrint['order_notes']))
                        <div class="thermal-notes">
                            <div class="thermal-notes-title">*** CATATAN KHUSUS ***</div>
                            <div>{{ $selectedTicketForPrint['order_notes'] }}</div>
                        </div>
                    @endif

                    <div class="thermal-divider"></div>

                    <!-- Item Lines -->
                    <div class="thermal-items">
                        <div class="thermal-items-head">
                            <span>QTY</span>
                            <span>ITEM PESANAN</span>
                        </div>
                        <div class="thermal-divider-dots"></div>
                        @foreach($selectedTicketForPrint['items'] as $item)
                            <div class="thermal-item-line">
                                <div class="thermal-item-qty font-bold">[{{ $item['qty'] }}x]</div>
                                <div class="thermal-item-detail">
                                    <div class="thermal-item-name font-bold">{{ strtoupper($item['name']) }}</div>
                                    @if(!empty($item['notes']))
                                        <div class="thermal-item-sub">>> {{ $item['notes'] }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="thermal-divider"></div>

                    <div class="thermal-footer">
                        <div>Waktu Cetak: {{ $selectedTicketForPrint['printed_at'] }}</div>
                        <div>*** HARAP PROSES DENGAN TELITI ***</div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="kds-modal-actions no-print">
                    <button type="button" onclick="window.print()" class="kds-btn-print-now">
                        🖨️ Cetak ke Printer Dapur (Print)
                    </button>
                    <button type="button" wire:click="closePrintTicketModal" class="kds-btn-close-modal">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>

<!-- ─────────────────────────────────────────────────────────────
     JAVASCRIPT AUDIO SYNTHESIZER & PRINT CONTROLLER
     Zero dependency audio tone generator for restaurant kitchen
───────────────────────────────────────────────────────────── -->
<script>
function kitchenDisplayApp() {
    return {
        currentTime: '',
        isFullscreen: false,
        audioCtx: null,

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);

            // Inisialisasi Audio Context
            window.addEventListener('click', () => this.initAudioContext(), { once: true });

            // Listeners dari Livewire
            window.addEventListener('play-kitchen-bell', () => {
                this.playOrderArrivalBell();
            });

            window.addEventListener('play-ready-bell', () => {
                this.playReadyBell();
            });

            window.addEventListener('play-success-sound', () => {
                this.playCompletedChord();
            });

            window.addEventListener('print-kitchen-ticket', () => {
                setTimeout(() => window.print(), 200);
            });
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        },

        initAudioContext() {
            if (!this.audioCtx) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (AudioContext) {
                    this.audioCtx = new AudioContext();
                }
            }
        },

        // Suara Lonceng Pesanan Baru Masuk ke Dapur (Two-tone Chime: Ting Tung)
        playOrderArrivalBell() {
            this.initAudioContext();
            if (!this.audioCtx) return;

            const ctx = this.audioCtx;
            if (ctx.state === 'suspended') {
                ctx.resume();
            }

            const now = ctx.currentTime;
            
            // Tone 1: High Ting (880Hz / A5)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(880, now);
            gain1.gain.setValueAtTime(0.4, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.8);

            // Tone 2: Lower Tung (587Hz / D5) sesaat kemudian
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(587, now + 0.25);
            gain2.gain.setValueAtTime(0.45, now + 0.25);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 1.2);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.25);
            osc2.stop(now + 1.2);
        },

        // Suara Makanan Siap Saji
        playReadyBell() {
            this.initAudioContext();
            if (!this.audioCtx) return;

            const ctx = this.audioCtx;
            if (ctx.state === 'suspended') ctx.resume();

            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(1046, now); // C6
            gain.gain.setValueAtTime(0.35, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now);
            osc.stop(now + 0.6);
        },

        // Suara Sukses / All Done
        playCompletedChord() {
            this.initAudioContext();
            if (!this.audioCtx) return;

            const ctx = this.audioCtx;
            if (ctx.state === 'suspended') ctx.resume();

            const now = ctx.currentTime;
            [523.25, 659.25, 783.99].forEach((freq) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, now);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.9);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.9);
            });
        },

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
                this.isFullscreen = true;
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                    this.isFullscreen = false;
                }
            }
        }
    };
}
</script>

<!-- ─────────────────────────────────────────────────────────────
     STYLING CSS KDS LENGKAP (DARK THEME RESMI DAPUR / TOUCHSCREEN)
───────────────────────────────────────────────────────────── -->
<style>
    /* ─── Variables & Themes ─── */
    .kds-root {
        --kds-bg: #0B1120;
        --kds-card-bg: #151F32;
        --kds-card-border: #1E2E4A;
        --kds-text: #F8FAFC;
        --kds-muted: #94A3B8;
        --kds-amber: #F59E0B;
        --kds-blue: #38BDF8;
        --kds-emerald: #10B981;
        --kds-red: #EF4444;
        
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: var(--kds-bg);
        color: var(--kds-text);
        min-height: 100vh;
        padding: 16px;
        box-sizing: border-box;
    }

    /* ─── Topbar ─── */
    .kds-topbar {
        background: #111C2E;
        border: 1px solid var(--kds-card-border);
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .kds-top-row {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    @media (min-width: 900px) {
        .kds-top-row {
            flex-direction: row;
            align-items: center;
        }
    }

    .kds-brand-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .kds-brand-icon {
        width: 42px;
        height: 42px;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 12px;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .kds-page-title {
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 0.05em;
        color: #FFFFFF;
        margin: 0;
        line-height: 1.1;
    }

    .kds-subtitle {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        color: var(--kds-muted);
        margin-top: 3px;
    }

    .kds-live-indicator {
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        background: #10B981;
        box-shadow: 0 0 8px #10B981;
        display: inline-block;
        animation: pulse 2s infinite;
    }

    .kds-time-clock {
        background: rgba(255,255,255,0.06);
        padding: 2px 8px;
        border-radius: 6px;
        font-family: monospace;
        color: #FBBF24;
        font-weight: 700;
    }

    /* ─── KPI Metrics ─── */
    .kds-metrics-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        width: 100%;
    }

    @media (min-width: 900px) {
        .kds-metrics-bar {
            width: auto;
            display: flex;
            gap: 10px;
        }
    }

    .kds-metric-card {
        background: #18263E;
        border: 1px solid #233658;
        border-radius: 10px;
        padding: 8px 14px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 90px;
        transition: all 0.15s ease;
    }

    .kds-metric-card:hover {
        transform: translateY(-1px);
    }

    .metric-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--kds-muted);
    }

    .metric-value {
        font-size: 18px;
        font-weight: 900;
        line-height: 1.1;
        margin-top: 2px;
    }

    .metric-pending .metric-value { color: #F59E0B; }
    .metric-cooking .metric-value { color: #38BDF8; }
    .metric-ready .metric-value { color: #10B981; }
    .metric-completed .metric-value { color: #94A3B8; }

    /* ─── Controls & Tabs ─── */
    .kds-control-row {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        border-top: 1px solid rgba(255,255,255,0.06);
        padding-top: 14px;
    }

    @media (min-width: 1024px) {
        .kds-control-row {
            flex-direction: row;
            align-items: center;
        }
    }

    .kds-tabs {
        display: flex;
        align-items: center;
        gap: 6px;
        overflow-x: auto;
        max-width: 100%;
        padding-bottom: 2px;
    }

    .kds-tab {
        background: #18263E;
        color: var(--kds-muted);
        border: 1px solid #233658;
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.15s ease;
    }

    .kds-tab:hover {
        background: #233658;
        color: #FFFFFF;
    }

    .kds-tab.active {
        background: #F59E0B;
        color: #0F172A;
        border-color: #F59E0B;
        font-weight: 900;
    }

    .kds-tab.tab-pending.active { background: #F59E0B; color: #0F172A; }
    .kds-tab.tab-cooking.active { background: #0284C7; color: #FFFFFF; }
    .kds-tab.tab-ready.active { background: #059669; color: #FFFFFF; }
    .kds-tab.tab-completed.active { background: #475569; color: #FFFFFF; }

    .kds-actions-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .kds-search-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .kds-search-icon {
        position: absolute;
        left: 10px;
        width: 14px;
        height: 14px;
        color: var(--kds-muted);
    }

    .kds-search-input {
        background: #18263E;
        border: 1px solid #233658;
        border-radius: 8px;
        padding: 6px 12px 6px 30px;
        color: #FFFFFF;
        font-size: 11px;
        width: 180px;
        outline: none;
        transition: border-color 0.15s ease;
    }

    .kds-search-input:focus {
        border-color: #F59E0B;
    }

    .kds-tool-btn {
        background: #18263E;
        border: 1px solid #233658;
        color: #FFFFFF;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .kds-tool-btn:hover {
        background: #233658;
    }

    .kds-tool-btn.active-tool {
        border-color: rgba(245, 158, 11, 0.5);
    }

    .kds-icon-tool {
        width: 14px;
        height: 14px;
    }

    /* ─── Cards Grid ─── */
    .kds-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 16px;
    }

    /* ─── Satu Kartu KDS ─── */
    .kds-card {
        background: var(--kds-card-bg);
        border: 2px solid var(--kds-card-border);
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.25);
        transition: border-color 0.2s ease, transform 0.15s ease;
    }

    .kds-card:hover {
        transform: translateY(-2px);
    }

    /* Border warna per status */
    .kds-card.status-pending { border-color: #F59E0B; }
    .kds-card.status-cooking { border-color: #0284C7; }
    .kds-card.status-ready { border-color: #10B981; }
    .kds-card.status-completed { border-color: #334155; opacity: 0.9; }

    .kds-card.card-delayed {
        border-color: #EF4444 !important;
        animation: pulseBorder 2s infinite;
    }

    @keyframes pulseBorder {
        0%, 100% { box-shadow: 0 0 0 rgba(239, 68, 68, 0); }
        50% { box-shadow: 0 0 12px rgba(239, 68, 68, 0.5); }
    }

    /* ─── Card Header ─── */
    .kds-card-header {
        background: #111A2C;
        padding: 12px 14px;
        border-bottom: 1px solid var(--kds-card-border);
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .kds-card-table-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .kds-table-badge {
        background: #F59E0B;
        color: #0F172A;
        font-weight: 900;
        font-size: 15px;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        letter-spacing: 0.05em;
    }

    .kds-card.status-cooking .kds-table-badge { background: #38BDF8; color: #0F172A; }
    .kds-card.status-ready .kds-table-badge { background: #10B981; color: #FFFFFF; }
    .kds-card.status-completed .kds-table-badge { background: #334155; color: #E2E8F0; }

    .kds-status-badge {
        font-size: 10px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .badge-pending { background: rgba(245, 158, 11, 0.2); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.4); }
    .badge-cooking { background: rgba(56, 189, 248, 0.2); color: #38BDF8; border: 1px solid rgba(56, 189, 248, 0.4); }
    .badge-ready { background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.4); }
    .badge-completed { background: rgba(148, 163, 184, 0.15); color: #94A3B8; border: 1px solid rgba(148, 163, 184, 0.3); }

    .pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 9999px;
        background: #F59E0B;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.8); }
    }

    .kds-card-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
    }

    .meta-inv {
        font-weight: 700;
        color: #E2E8F0;
    }

    .meta-time {
        font-size: 10.5px;
        color: var(--kds-muted);
    }

    .time-delayed {
        color: #F87171 !important;
        font-weight: 800;
    }

    .kds-card-customer-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        color: var(--kds-muted);
        border-top: 1px dashed rgba(255,255,255,0.06);
        padding-top: 5px;
    }

    .customer-name {
        font-weight: 600;
        color: #CBD5E1;
    }

    .cashier-name {
        font-size: 10px;
    }

    /* ─── Notes Box ─── */
    .kds-order-notes {
        background: rgba(245, 158, 11, 0.12);
        border-left: 3px solid #F59E0B;
        padding: 8px 12px;
        margin: 10px 14px 0 14px;
        border-radius: 6px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .notes-icon {
        font-size: 13px;
    }

    .notes-title {
        font-size: 9.5px;
        font-weight: 800;
        text-transform: uppercase;
        color: #FBBF24;
    }

    .notes-text {
        font-size: 11.5px;
        font-weight: 700;
        color: #FFFFFF;
        margin-top: 1px;
        line-height: 1.3;
    }

    /* ─── Items List ─── */
    .kds-items-list {
        padding: 12px 14px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .kds-items-header {
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--kds-muted);
        margin-bottom: 2px;
    }

    .kds-item-row {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #111A2C;
        border: 1px solid #1E2D47;
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease;
    }

    .kds-item-row:hover {
        background: #192742;
    }

    .kds-item-row.item-checked {
        opacity: 0.6;
        background: rgba(16, 185, 129, 0.08);
        border-color: rgba(16, 185, 129, 0.3);
    }

    .kds-item-row.item-checked .kds-item-name {
        text-decoration: line-through;
        color: #94A3B8;
    }

    .kds-item-qty {
        font-size: 16px;
        font-weight: 900;
        color: #F59E0B;
        min-width: 28px;
        text-align: center;
    }

    .kds-item-info {
        flex: 1;
    }

    .kds-item-name {
        font-size: 13px;
        font-weight: 800;
        color: #FFFFFF;
        line-height: 1.2;
    }

    .kds-item-note {
        font-size: 10px;
        color: #FBBF24;
        margin-top: 2px;
        font-style: italic;
    }

    .kds-item-check-btn {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 900;
    }

    .check-icon-done {
        color: #10B981;
    }

    .check-icon-empty {
        color: #475569;
    }

    /* ─── Print Status Row ─── */
    .kds-print-status-row {
        padding: 6px 14px;
        background: #0E1626;
        border-top: 1px solid rgba(255,255,255,0.04);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 10px;
    }

    .kds-btn-print-ticket {
        background: #18263E;
        border: 1px solid #233658;
        color: #E2E8F0;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        transition: all 0.15s ease;
    }

    .kds-btn-print-ticket:hover {
        background: #F59E0B;
        color: #0F172A;
        border-color: #F59E0B;
    }

    /* ─── Card Footer Actions ─── */
    .kds-card-footer {
        padding: 10px 14px;
        background: #111A2C;
        border-top: 1px solid var(--kds-card-border);
    }

    .kds-action-btn {
        width: 100%;
        padding: 11px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.05em;
        cursor: pointer;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.15s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    }

    .kds-action-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.1);
    }

    .btn-start-cooking {
        background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%);
        color: #FFFFFF;
    }

    .btn-mark-ready {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: #FFFFFF;
    }

    .btn-mark-completed {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        color: #0F172A;
    }

    /* ─── Full Selesai Stamp ─── */
    .kds-full-completed-stamp {
        background: rgba(16, 185, 129, 0.15);
        border: 1.5px solid #10B981;
        border-radius: 9px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .stamp-icon {
        width: 28px;
        height: 28px;
        border-radius: 9999px;
        background: #10B981;
        color: #0F172A;
        font-weight: 900;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stamp-content {
        flex: 1;
    }

    .stamp-title {
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.06em;
        color: #34D399;
    }

    .stamp-sub {
        font-size: 9.5px;
        color: #94A3B8;
        margin-top: 1px;
    }

    .stamp-undo-btn {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        color: #CBD5E1;
        width: 24px;
        height: 24px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stamp-undo-btn:hover {
        background: rgba(255,255,255,0.2);
    }

    /* ─── Empty State ─── */
    .kds-empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #111C2E;
        border: 1px dashed #233658;
        border-radius: 16px;
        max-width: 500px;
        margin: 40px auto;
    }

    .kds-empty-icon {
        width: 54px;
        height: 54px;
        border-radius: 9999px;
        background: rgba(16, 185, 129, 0.15);
        color: #34D399;
        font-size: 26px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px auto;
    }

    .kds-empty-title {
        font-size: 15px;
        font-weight: 800;
        color: #FFFFFF;
        margin-bottom: 6px;
    }

    .kds-empty-desc {
        font-size: 12px;
        color: var(--kds-muted);
        line-height: 1.4;
    }

    .kds-btn-empty {
        margin-top: 16px;
        background: #F59E0B;
        color: #0F172A;
        font-weight: 800;
        font-size: 11px;
        padding: 8px 16px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
    }

    /* ─── Modal Struk Dapur ─── */
    .kds-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 999;
        background: rgba(0,0,0,0.8);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .kds-modal-sheet {
        background: #FFFFFF;
        border-radius: 16px;
        max-width: 360px;
        width: 100%;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        color: #000000;
    }

    .kds-modal-bar {
        padding: 12px 16px;
        background: #F1F5F9;
        border-bottom: 1px solid #E2E8F0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
    }

    .kds-modal-close {
        background: none;
        border: none;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        color: #64748b;
    }

    .kds-thermal-paper {
        padding: 16px;
        background: #FFFFFF;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        color: #000000;
        line-height: 1.35;
    }

    .thermal-header {
        text-align: center;
    }

    .thermal-title {
        font-size: 15px;
        font-weight: 900;
        letter-spacing: 0.08em;
    }

    .thermal-subtitle {
        font-size: 11px;
        color: #333333;
        margin-top: 2px;
    }

    .thermal-divider {
        border-top: 1px dashed #000000;
        margin: 8px 0;
    }

    .thermal-divider-dots {
        border-top: 1px dotted #888888;
        margin: 4px 0;
    }

    .thermal-table-highlight {
        font-size: 17px;
        font-weight: 900;
        text-align: center;
        background: #000000;
        color: #FFFFFF;
        padding: 4px;
        border-radius: 4px;
        margin-bottom: 6px;
    }

    .thermal-row {
        display: flex;
        justify-content: space-between;
        font-size: 11.5px;
        margin-bottom: 2px;
    }

    .thermal-notes {
        background: #F4F4F5;
        border: 1px dashed #000000;
        padding: 6px;
        margin: 6px 0;
        font-size: 11px;
    }

    .thermal-notes-title {
        font-weight: 900;
        margin-bottom: 2px;
    }

    .thermal-items {
        margin: 6px 0;
    }

    .thermal-items-head {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        font-weight: 800;
    }

    .thermal-item-line {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 6px;
    }

    .thermal-item-qty {
        font-size: 14px;
        min-width: 30px;
    }

    .thermal-item-name {
        font-size: 12px;
    }

    .thermal-item-sub {
        font-size: 10.5px;
        color: #444444;
    }

    .thermal-footer {
        text-align: center;
        font-size: 10px;
        color: #555555;
        margin-top: 6px;
    }

    .kds-modal-actions {
        padding: 12px 16px;
        background: #F8FAFC;
        border-top: 1px solid #E2E8F0;
        display: flex;
        gap: 8px;
    }

    .kds-btn-print-now {
        flex: 1;
        background: #0F172A;
        color: #FFFFFF;
        padding: 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        border: none;
        cursor: pointer;
    }

    .kds-btn-close-modal {
        background: #E2E8F0;
        color: #334155;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
    }

    /* ─── PRINT CSS THERMAL BON DAPUR ─── */
    @media print {
        body * {
            visibility: hidden !important;
        }

        #kitchen-ticket-print, #kitchen-ticket-print * {
            visibility: visible !important;
        }

        #kitchen-ticket-print {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            max-width: 78mm !important;
            margin: 0 !important;
            padding: 2mm !important;
            color: #000000 !important;
            background: #FFFFFF !important;
        }

        .no-print {
            display: none !important;
        }

        @page {
            size: auto;
            margin: 0;
        }
    }
</style>
