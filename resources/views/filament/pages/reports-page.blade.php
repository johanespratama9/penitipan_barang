<x-filament-panels::page>
    <style>
        /* ─────────────────────────────────────────────────────────────
           FILAMENT BUSINESS REPORT STYLES (Light & Dark Mode Compatible)
        ───────────────────────────────────────────────────────────── */
        .rep-wrap {
            --rep-bg-card: #ffffff;
            --rep-bg-card-subtle: #f9fafb;
            --rep-border: #e5e7eb;
            --rep-text-main: #111827;
            --rep-text-muted: #6b7280;
            --rep-hover-row: #f9fafb;
            --rep-primary: #d97706;
            --rep-primary-subtle: rgba(217, 119, 6, 0.1);
        }

        :is(.dark, .dark *) .rep-wrap {
            --rep-bg-card: #18181b;
            --rep-bg-card-subtle: #27272a;
            --rep-border: rgba(255, 255, 255, 0.08);
            --rep-text-main: #f4f4f5;
            --rep-text-muted: #a1a1aa;
            --rep-hover-row: rgba(255, 255, 255, 0.03);
            --rep-primary: #f59e0b;
            --rep-primary-subtle: rgba(245, 158, 11, 0.15);
        }

        /* Card Container */
        .rep-card {
            background-color: var(--rep-bg-card);
            border: 1px solid var(--rep-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Stat KPI Card */
        .rep-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .rep-kpi-card {
            background-color: var(--rep-bg-card);
            border: 1px solid var(--rep-border);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
        }
        .rep-kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .rep-kpi-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--rep-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .rep-kpi-val {
            font-size: 20px;
            font-weight: 700;
            color: var(--rep-text-main);
            margin-top: 2px;
            line-height: 1.2;
        }

        /* Navigation Tabs Bar */
        .rep-tabs-bar {
            display: flex;
            align-items: center;
            gap: 6px;
            background-color: var(--rep-bg-card-subtle);
            padding: 5px;
            border-radius: 12px;
            border: 1px solid var(--rep-border);
            overflow-x: auto;
        }
        .rep-tab-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            background: transparent;
            color: var(--rep-text-muted);
            transition: all 0.15s ease;
        }
        .rep-tab-btn:hover {
            color: var(--rep-text-main);
            background-color: rgba(0, 0, 0, 0.03);
        }
        :is(.dark, .dark *) .rep-tab-btn:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .rep-tab-btn.active {
            background-color: var(--rep-bg-card);
            color: var(--rep-text-main);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .rep-tab-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 99px;
            background-color: var(--rep-primary-subtle);
            color: var(--rep-primary);
        }

        /* Filter Toolbar Card */
        .rep-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 18px;
            background-color: var(--rep-bg-card);
            border: 1px solid var(--rep-border);
            border-radius: 12px;
            margin-top: 16px;
            margin-bottom: 20px;
        }
        .rep-date-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .rep-input-group {
            display: flex;
            align-items: center;
            border: 1px solid var(--rep-border);
            background-color: var(--rep-bg-card-subtle);
            border-radius: 8px;
            overflow: hidden;
            font-size: 12px;
        }
        .rep-input-label {
            padding: 6px 10px;
            color: var(--rep-text-muted);
            font-weight: 600;
            background-color: rgba(0, 0, 0, 0.03);
            border-right: 1px solid var(--rep-border);
        }
        :is(.dark, .dark *) .rep-input-label {
            background-color: rgba(255, 255, 255, 0.04);
        }
        .rep-input-date {
            border: none;
            padding: 6px 10px;
            font-size: 12px;
            background: transparent;
            color: var(--rep-text-main);
            outline: none;
        }

        /* Table Card Header */
        .rep-table-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--rep-border);
        }
        .rep-table-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--rep-text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .rep-search-wrap {
            position: relative;
            min-width: 240px;
            max-width: 320px;
            flex: 1;
        }
        .rep-search-input {
            width: 100%;
            height: 36px;
            padding: 0 32px 0 34px;
            font-size: 13px;
            background-color: var(--rep-bg-card-subtle);
            border: 1px solid var(--rep-border);
            border-radius: 8px;
            color: var(--rep-text-main);
            outline: none;
            transition: border-color 0.15s;
        }
        .rep-search-input:focus {
            border-color: var(--rep-primary);
        }
        .rep-search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--rep-text-muted);
            pointer-events: none;
        }
        .rep-search-clear {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--rep-text-muted);
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
        }

        /* Clean Table Elements */
        .rep-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }
        .rep-th {
            background-color: var(--rep-bg-card-subtle);
            color: var(--rep-text-muted);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 16px;
            border-bottom: 1px solid var(--rep-border);
            white-space: nowrap;
        }
        .rep-tr {
            border-bottom: 1px solid var(--rep-border);
            transition: background-color 0.1s ease;
        }
        .rep-tr:last-child {
            border-bottom: none;
        }
        .rep-tr:hover {
            background-color: var(--rep-hover-row);
        }
        .rep-td {
            padding: 13px 16px;
            color: var(--rep-text-main);
            vertical-align: middle;
            white-space: nowrap;
        }

        /* Monospace / Code styling */
        .rep-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            font-weight: 600;
            color: var(--rep-text-main);
        }

        /* Pill Badges */
        .rep-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 9px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .rep-badge-success {
            background-color: rgba(16, 185, 129, 0.12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }
        :is(.dark, .dark *) .rep-badge-success {
            color: #34d399;
        }
        .rep-badge-info {
            background-color: rgba(59, 130, 246, 0.12);
            color: #2563eb;
            border: 1px solid rgba(59, 130, 246, 0.25);
        }
        :is(.dark, .dark *) .rep-badge-info {
            color: #60a5fa;
        }
        .rep-badge-warning {
            background-color: rgba(245, 158, 11, 0.12);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }
        :is(.dark, .dark *) .rep-badge-warning {
            color: #fbbf24;
        }
        .rep-badge-danger {
            background-color: rgba(239, 68, 68, 0.12);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }
        :is(.dark, .dark *) .rep-badge-danger {
            color: #f87171;
        }
        .rep-badge-gray {
            background-color: rgba(107, 114, 128, 0.12);
            color: #4b5563;
            border: 1px solid rgba(107, 114, 128, 0.25);
        }
        :is(.dark, .dark *) .rep-badge-gray {
            color: #9ca3af;
        }

        /* Buttons */
        .rep-btn-export {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            background-color: #059669;
            color: #ffffff;
            border: none;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: background-color 0.15s;
        }
        .rep-btn-export:hover {
            background-color: #047857;
        }

        /* Table Summary Footer */
        .rep-tfoot {
            background-color: var(--rep-bg-card-subtle);
            border-top: 2px solid var(--rep-border);
            font-weight: 700;
        }
        .rep-tfoot td {
            padding: 13px 16px;
            font-size: 13px;
            color: var(--rep-text-main);
        }

        /* Empty State */
        .rep-empty {
            padding: 48px 24px;
            text-align: center;
            color: var(--rep-text-muted);
        }
        .rep-empty-icon {
            margin: 0 auto 12px;
            width: 48px;
            height: 48px;
            color: var(--rep-text-muted);
            opacity: 0.6;
        }
        .rep-empty-heading {
            font-size: 15px;
            font-weight: 700;
            color: var(--rep-text-main);
            margin-bottom: 4px;
        }
        .rep-empty-desc {
            font-size: 13px;
            color: var(--rep-text-muted);
        }
    </style>

    <div class="rep-wrap">

        @php
            $salesList     = $this->salesReport;
            $consignorList = $this->consignorReport;
            $productList   = $this->productReport;
            $paymentList   = $this->paymentReport;
        @endphp

        <!-- ══════════════════════════════════════════════════════════════════
             1. EXECUTIVE KPI CARDS (Live Metrics per Tab)
        ══════════════════════════════════════════════════════════════════ -->
        @if($activeTab === 'sales')
            @php
                $totalOmzet = $salesList->sum('total');
                $totalTx    = $salesList->count();
                $avgTx      = $totalTx > 0 ? $totalOmzet / $totalTx : 0;
            @endphp
            <div class="rep-kpi-grid">
                <div class="rep-kpi-card">
                    <div class="rep-kpi-icon" style="background-color:rgba(16,185,129,0.12);color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <div>
                        <div class="rep-kpi-label">Total Omzet Penjualan</div>
                        <div class="rep-kpi-val" style="color:#059669;">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="rep-kpi-card">
                    <div class="rep-kpi-icon" style="background-color:rgba(59,130,246,0.12);color:#2563eb;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    </div>
                    <div>
                        <div class="rep-kpi-label">Frekuensi Transaksi</div>
                        <div class="rep-kpi-val">{{ number_format($totalTx) }} Transaksi</div>
                    </div>
                </div>
                <div class="rep-kpi-card">
                    <div class="rep-kpi-icon" style="background-color:rgba(245,158,11,0.12);color:#d97706;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <div>
                        <div class="rep-kpi-label">Rata-rata Nilai Transaksi</div>
                        <div class="rep-kpi-val">Rp {{ number_format($avgTx, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'consignor')
            @php
                $totSalesC = $consignorList->sum('total_sales');
                $totEarned = $consignorList->sum('total_earned');
                $totBal    = $consignorList->sum('balance');
            @endphp
            <div class="rep-kpi-grid">
                <div class="rep-kpi-card">
                    <div class="rep-kpi-icon" style="background-color:rgba(59,130,246,0.12);color:#2563eb;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <div>
                        <div class="rep-kpi-label">Total Penjualan Barang</div>
                        <div class="rep-kpi-val">Rp {{ number_format($totSalesC, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="rep-kpi-card">
                    <div class="rep-kpi-icon" style="background-color:rgba(16,185,129,0.12);color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <div class="rep-kpi-label">Hak Bersih Penitip</div>
                        <div class="rep-kpi-val" style="color:#059669;">Rp {{ number_format($totEarned, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="rep-kpi-card">
                    <div class="rep-kpi-icon" style="background-color:rgba(245,158,11,0.12);color:#d97706;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <div>
                        <div class="rep-kpi-label">Sisa Saldo Belum Dicairkan</div>
                        <div class="rep-kpi-val" style="color:#d97706;">Rp {{ number_format($totBal, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'products')
            @php
                $totItems   = $productList->count();
                $totSoldQty = $productList->sum('sold_qty');
                $totOmzetP  = $productList->sum('omzet');
            @endphp
            <div class="rep-kpi-grid">
                <div class="rep-kpi-card">
                    <div class="rep-kpi-icon" style="background-color:rgba(59,130,246,0.12);color:#2563eb;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
                    </div>
                    <div>
                        <div class="rep-kpi-label">Katalog Produk Aktif</div>
                        <div class="rep-kpi-val">{{ number_format($totItems) }} Item</div>
                    </div>
                </div>
                <div class="rep-kpi-card">
                    <div class="rep-kpi-icon" style="background-color:rgba(16,185,129,0.12);color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <div class="rep-kpi-label">Total Unit Terjual</div>
                        <div class="rep-kpi-val">{{ number_format($totSoldQty) }} pcs</div>
                    </div>
                </div>
                <div class="rep-kpi-card">
                    <div class="rep-kpi-icon" style="background-color:rgba(217,119,6,0.12);color:#d97706;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <div>
                        <div class="rep-kpi-label">Akumulasi Omzet Barang</div>
                        <div class="rep-kpi-val">Rp {{ number_format($totOmzetP, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'payments')
            @php
                $totPay = $paymentList->sum('amount');
                $cntPay = $paymentList->count();
            @endphp
            <div class="rep-kpi-grid">
                <div class="rep-kpi-card">
                    <div class="rep-kpi-icon" style="background-color:rgba(16,185,129,0.12);color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <div>
                        <div class="rep-kpi-label">Total Dana Dicairkan</div>
                        <div class="rep-kpi-val" style="color:#059669;">Rp {{ number_format($totPay, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="rep-kpi-card">
                    <div class="rep-kpi-icon" style="background-color:rgba(59,130,246,0.12);color:#2563eb;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                    </div>
                    <div>
                        <div class="rep-kpi-label">Frekuensi Pencairan</div>
                        <div class="rep-kpi-val">{{ number_format($cntPay) }} Kali</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- ══════════════════════════════════════════════════════════════════
             2. CLEAN TABS NAVIGATION
        ══════════════════════════════════════════════════════════════════ -->
        <div class="rep-tabs-bar">
            <button
                type="button"
                wire:click="$set('activeTab', 'sales')"
                class="rep-tab-btn {{ $activeTab === 'sales' ? 'active' : '' }}"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                <span>Laporan Penjualan</span>
                <span class="rep-tab-badge">{{ count($salesList) }}</span>
            </button>

            <button
                type="button"
                wire:click="$set('activeTab', 'consignor')"
                class="rep-tab-btn {{ $activeTab === 'consignor' ? 'active' : '' }}"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                <span>Laporan Penitip &amp; Saldo</span>
                <span class="rep-tab-badge">{{ count($consignorList) }}</span>
            </button>

            <button
                type="button"
                wire:click="$set('activeTab', 'products')"
                class="rep-tab-btn {{ $activeTab === 'products' ? 'active' : '' }}"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
                <span>Laporan Produk &amp; Stok</span>
                <span class="rep-tab-badge">{{ count($productList) }}</span>
            </button>

            <button
                type="button"
                wire:click="$set('activeTab', 'payments')"
                class="rep-tab-btn {{ $activeTab === 'payments' ? 'active' : '' }}"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                <span>Laporan Pencairan Dana</span>
                <span class="rep-tab-badge">{{ count($paymentList) }}</span>
            </button>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════
             3. TOOLBAR: DATE FILTERS & EXPORT ACTION
        ══════════════════════════════════════════════════════════════════ -->
        <div class="rep-toolbar">
            @if(in_array($activeTab, ['sales', 'payments']))
                <div class="rep-date-wrap">
                    <div class="rep-input-group">
                        <span class="rep-input-label">Dari</span>
                        <input type="date" wire:model.live="startDate" class="rep-input-date" />
                    </div>
                    <div class="rep-input-group">
                        <span class="rep-input-label">Sampai</span>
                        <input type="date" wire:model.live="endDate" class="rep-input-date" />
                    </div>
                </div>
            @else
                <div style="font-size:12px;color:var(--rep-text-muted);">
                    Data di bawah dihitung real-time dari seluruh aktivitas konsinyasi.
                </div>
            @endif

            <button
                type="button"
                wire:click="exportCsv('{{ $activeTab }}')"
                class="rep-btn-export"
                title="Download CSV"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Export CSV</span>
            </button>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════
             4. TABLE CARD CONTAINER
        ══════════════════════════════════════════════════════════════════ -->
        <div class="rep-card">

            <!-- ── TAB 1: PENJUALAN ── -->
            @if($activeTab === 'sales')
                <div class="rep-table-header">
                    <div class="rep-table-title">
                        <span>Daftar Transaksi Penjualan</span>
                        <span class="rep-badge rep-badge-gray">{{ count($salesList) }} Record</span>
                    </div>
                    <div class="rep-search-wrap">
                        <svg class="rep-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input
                            type="text"
                            wire:model.live.debounce.250ms="search"
                            placeholder="Cari no invoice, kasir, pelanggan..."
                            class="rep-search-input"
                        />
                        @if($search)
                            <button type="button" wire:click="$set('search','')" class="rep-search-clear">✕</button>
                        @endif
                    </div>
                </div>

                @if(count($salesList) > 0)
                    <div style="overflow-x:auto;">
                        <table class="rep-table">
                            <thead>
                                <tr>
                                    <th class="rep-th">No. Invoice</th>
                                    <th class="rep-th">Waktu Transaksi</th>
                                    <th class="rep-th">Kasir</th>
                                    <th class="rep-th">Pelanggan</th>
                                    <th class="rep-th" style="text-align:center;">Pembayaran</th>
                                    <th class="rep-th" style="text-align:right;">Total Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotal = 0; @endphp
                                @foreach($salesList as $sale)
                                    @php $grandTotal += (float) $sale->total; @endphp
                                    <tr class="rep-tr">
                                        <td class="rep-td rep-code">{{ $sale->invoice_number }}</td>
                                        <td class="rep-td" style="color:var(--rep-text-muted);">{{ $sale->sold_at->format('d M Y, H:i') }}</td>
                                        <td class="rep-td" style="font-weight:600;">{{ $sale->cashier?->name ?? '-' }}</td>
                                        <td class="rep-td">{{ $sale->customer_name ?: 'Umum' }}</td>
                                        <td class="rep-td" style="text-align:center;">
                                            @php
                                                $pClass = match(strtolower($sale->payment_method)) {
                                                    'cash' => 'rep-badge-success',
                                                    'qris' => 'rep-badge-info',
                                                    'transfer' => 'rep-badge-warning',
                                                    default => 'rep-badge-gray',
                                                };
                                            @endphp
                                            <span class="rep-badge {{ $pClass }}">{{ strtoupper($sale->payment_method) }}</span>
                                        </td>
                                        <td class="rep-td rep-code" style="text-align:right;font-size:13px;font-weight:700;">
                                            Rp {{ number_format($sale->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="rep-tfoot">
                                <tr>
                                    <td colspan="5" style="text-align:right;text-transform:uppercase;font-size:12px;letter-spacing:0.04em;">
                                        Total Keseluruhan:
                                    </td>
                                    <td style="text-align:right;font-size:15px;color:#059669;" class="rep-code">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="rep-empty">
                        <svg class="rep-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <div class="rep-empty-heading">Tidak Ada Data Transaksi</div>
                        <div class="rep-empty-desc">Tidak ada penjualan pada rentang tanggal atau kata kunci pencarian ini.</div>
                    </div>
                @endif

            <!-- ── TAB 2: PENITIP & SALDO ── -->
            @elseif($activeTab === 'consignor')
                <div class="rep-table-header">
                    <div class="rep-table-title">
                        <span>Laporan Penitip &amp; Saldo</span>
                        <span class="rep-badge rep-badge-gray">{{ count($consignorList) }} Penitip</span>
                    </div>
                    <div class="rep-search-wrap">
                        <svg class="rep-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input
                            type="text"
                            wire:model.live.debounce.250ms="search"
                            placeholder="Cari kode, nama, telepon..."
                            class="rep-search-input"
                        />
                        @if($search)
                            <button type="button" wire:click="$set('search','')" class="rep-search-clear">✕</button>
                        @endif
                    </div>
                </div>

                @if(count($consignorList) > 0)
                    <div style="overflow-x:auto;">
                        <table class="rep-table">
                            <thead>
                                <tr>
                                    <th class="rep-th">Kode</th>
                                    <th class="rep-th">Nama Penitip</th>
                                    <th class="rep-th">Telepon</th>
                                    <th class="rep-th" style="text-align:center;">Barang (Terjual/Total)</th>
                                    <th class="rep-th" style="text-align:right;">Total Penjualan</th>
                                    <th class="rep-th" style="text-align:right;">Komisi Toko</th>
                                    <th class="rep-th" style="text-align:right;">Hak Penitip</th>
                                    <th class="rep-th" style="text-align:right;">Sudah Dibayar</th>
                                    <th class="rep-th" style="text-align:right;">Saldo Tersisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sumS = 0; $sumC = 0; $sumE = 0; $sumP = 0; $sumB = 0;
                                @endphp
                                @foreach($consignorList as $c)
                                    @php
                                        $sumS += $c['total_sales'];
                                        $sumC += $c['total_commission'];
                                        $sumE += $c['total_earned'];
                                        $sumP += $c['total_paid'];
                                        $sumB += $c['balance'];
                                    @endphp
                                    <tr class="rep-tr">
                                        <td class="rep-td rep-code">{{ $c['code'] }}</td>
                                        <td class="rep-td" style="font-weight:600;">{{ $c['name'] }}</td>
                                        <td class="rep-td" style="color:var(--rep-text-muted);font-size:12px;">{{ $c['phone'] }}</td>
                                        <td class="rep-td" style="text-align:center;">
                                            <span style="font-weight:700;">{{ $c['sold_products'] }}</span>
                                            <span style="color:var(--rep-text-muted);margin:0 2px;">/</span>
                                            <span style="color:var(--rep-text-muted);">{{ $c['total_products'] }}</span>
                                        </td>
                                        <td class="rep-td rep-code" style="text-align:right;">Rp {{ number_format($c['total_sales'], 0, ',', '.') }}</td>
                                        <td class="rep-td rep-code" style="text-align:right;color:var(--rep-text-muted);">Rp {{ number_format($c['total_commission'], 0, ',', '.') }}</td>
                                        <td class="rep-td rep-code" style="text-align:right;font-weight:700;">Rp {{ number_format($c['total_earned'], 0, ',', '.') }}</td>
                                        <td class="rep-td rep-code" style="text-align:right;color:#059669;font-weight:600;">Rp {{ number_format($c['total_paid'], 0, ',', '.') }}</td>
                                        <td class="rep-td rep-code" style="text-align:right;font-weight:800;color:{{ $c['balance'] > 0 ? '#d97706' : 'var(--rep-text-muted)' }};">
                                            Rp {{ number_format($c['balance'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="rep-tfoot">
                                <tr>
                                    <td colspan="4" style="text-align:right;text-transform:uppercase;font-size:12px;">Total:</td>
                                    <td style="text-align:right;" class="rep-code">Rp {{ number_format($sumS, 0, ',', '.') }}</td>
                                    <td style="text-align:right;color:var(--rep-text-muted);" class="rep-code">Rp {{ number_format($sumC, 0, ',', '.') }}</td>
                                    <td style="text-align:right;font-weight:700;" class="rep-code">Rp {{ number_format($sumE, 0, ',', '.') }}</td>
                                    <td style="text-align:right;color:#059669;" class="rep-code">Rp {{ number_format($sumP, 0, ',', '.') }}</td>
                                    <td style="text-align:right;font-size:15px;color:#d97706;" class="rep-code">Rp {{ number_format($sumB, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="rep-empty">
                        <svg class="rep-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <div class="rep-empty-heading">Belum Ada Data Penitip</div>
                        <div class="rep-empty-desc">Tidak ada data penitip yang sesuai dengan kata kunci ini.</div>
                    </div>
                @endif

            <!-- ── TAB 3: PRODUK & STOK ── -->
            @elseif($activeTab === 'products')
                <div class="rep-table-header">
                    <div class="rep-table-title">
                        <span>Laporan Produk &amp; Stok</span>
                        <span class="rep-badge rep-badge-gray">{{ count($productList) }} Item</span>
                    </div>
                    <div class="rep-search-wrap">
                        <svg class="rep-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input
                            type="text"
                            wire:model.live.debounce.250ms="search"
                            placeholder="Cari kode, nama produk, kategori..."
                            class="rep-search-input"
                        />
                        @if($search)
                            <button type="button" wire:click="$set('search','')" class="rep-search-clear">✕</button>
                        @endif
                    </div>
                </div>

                @if(count($productList) > 0)
                    <div style="overflow-x:auto;">
                        <table class="rep-table">
                            <thead>
                                <tr>
                                    <th class="rep-th">Kode</th>
                                    <th class="rep-th">Nama Barang</th>
                                    <th class="rep-th">Kategori</th>
                                    <th class="rep-th">Penitip</th>
                                    <th class="rep-th" style="text-align:right;">Harga Jual</th>
                                    <th class="rep-th" style="text-align:center;">Sisa Stok</th>
                                    <th class="rep-th" style="text-align:center;">Terjual</th>
                                    <th class="rep-th" style="text-align:right;">Total Omzet</th>
                                    <th class="rep-th" style="text-align:center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totOmz = 0; @endphp
                                @foreach($productList as $p)
                                    @php $totOmz += $p['omzet']; @endphp
                                    <tr class="rep-tr">
                                        <td class="rep-td rep-code">{{ $p['code'] }}</td>
                                        <td class="rep-td" style="font-weight:600;">{{ $p['name'] }}</td>
                                        <td class="rep-td" style="color:var(--rep-text-muted);">{{ $p['category'] }}</td>
                                        <td class="rep-td" style="color:var(--rep-text-muted);">{{ $p['consignor'] }}</td>
                                        <td class="rep-td rep-code" style="text-align:right;">Rp {{ number_format($p['price'], 0, ',', '.') }}</td>
                                        <td class="rep-td" style="text-align:center;">
                                            <span class="rep-badge {{ $p['stock'] > 0 ? 'rep-badge-success' : 'rep-badge-danger' }}">
                                                {{ $p['stock'] }}
                                            </span>
                                        </td>
                                        <td class="rep-td" style="text-align:center;font-weight:700;">{{ $p['sold_qty'] }}</td>
                                        <td class="rep-td rep-code" style="text-align:right;font-weight:700;">Rp {{ number_format($p['omzet'], 0, ',', '.') }}</td>
                                        <td class="rep-td" style="text-align:center;">
                                            @php
                                                $stClass = match($p['status']) {
                                                    'available' => 'rep-badge-success',
                                                    'sold' => 'rep-badge-info',
                                                    'pending' => 'rep-badge-warning',
                                                    'returned' => 'rep-badge-gray',
                                                    'rejected', 'expired' => 'rep-badge-danger',
                                                    default => 'rep-badge-gray',
                                                };
                                                $stLabel = match($p['status']) {
                                                    'available' => 'Tersedia',
                                                    'sold' => 'Terjual',
                                                    'pending' => 'Pending',
                                                    'returned' => 'Dikembalikan',
                                                    'rejected' => 'Ditolak',
                                                    'expired' => 'Kedaluwarsa',
                                                    default => ucfirst($p['status']),
                                                };
                                            @endphp
                                            <span class="rep-badge {{ $stClass }}">{{ $stLabel }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="rep-tfoot">
                                <tr>
                                    <td colspan="7" style="text-align:right;text-transform:uppercase;font-size:12px;">Total Seluruh Omzet:</td>
                                    <td style="text-align:right;font-size:15px;color:#059669;" class="rep-code">Rp {{ number_format($totOmz, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="rep-empty">
                        <svg class="rep-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
                        <div class="rep-empty-heading">Belum Ada Data Produk</div>
                        <div class="rep-empty-desc">Tidak ada produk yang cocok dengan kata kunci pencarian.</div>
                    </div>
                @endif

            <!-- ── TAB 4: PENCAIRAN DANA ── -->
            @elseif($activeTab === 'payments')
                <div class="rep-table-header">
                    <div class="rep-table-title">
                        <span>Laporan Pencairan Dana</span>
                        <span class="rep-badge rep-badge-gray">{{ count($paymentList) }} Pencairan</span>
                    </div>
                    <div class="rep-search-wrap">
                        <svg class="rep-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input
                            type="text"
                            wire:model.live.debounce.250ms="search"
                            placeholder="Cari no pembayaran, penitip..."
                            class="rep-search-input"
                        />
                        @if($search)
                            <button type="button" wire:click="$set('search','')" class="rep-search-clear">✕</button>
                        @endif
                    </div>
                </div>

                @if(count($paymentList) > 0)
                    <div style="overflow-x:auto;">
                        <table class="rep-table">
                            <thead>
                                <tr>
                                    <th class="rep-th">No. Pembayaran</th>
                                    <th class="rep-th">Tanggal</th>
                                    <th class="rep-th">Penitip</th>
                                    <th class="rep-th" style="text-align:center;">Metode</th>
                                    <th class="rep-th">No. Referensi</th>
                                    <th class="rep-th" style="text-align:right;">Nominal Dibayar</th>
                                    <th class="rep-th">Diproses Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totPaySum = 0; @endphp
                                @foreach($paymentList as $pay)
                                    @php $totPaySum += (float) $pay->amount; @endphp
                                    <tr class="rep-tr">
                                        <td class="rep-td rep-code">{{ $pay->payment_number }}</td>
                                        <td class="rep-td" style="color:var(--rep-text-muted);">{{ $pay->paid_at->format('d M Y, H:i') }}</td>
                                        <td class="rep-td" style="font-weight:600;">{{ $pay->consignor?->name ?? '-' }}</td>
                                        <td class="rep-td" style="text-align:center;">
                                            @php
                                                $pmClass = match(strtolower($pay->payment_method)) {
                                                    'cash' => 'rep-badge-success',
                                                    'transfer' => 'rep-badge-warning',
                                                    default => 'rep-badge-gray',
                                                };
                                            @endphp
                                            <span class="rep-badge {{ $pmClass }}">{{ strtoupper($pay->payment_method) }}</span>
                                        </td>
                                        <td class="rep-td rep-code" style="color:var(--rep-text-muted);font-size:12px;">{{ $pay->reference_number ?: '-' }}</td>
                                        <td class="rep-td rep-code" style="text-align:right;font-weight:700;color:#059669;">
                                            Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="rep-td" style="color:var(--rep-text-muted);">{{ $pay->admin?->name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="rep-tfoot">
                                <tr>
                                    <td colspan="5" style="text-align:right;text-transform:uppercase;font-size:12px;">Total Pencairan:</td>
                                    <td style="text-align:right;font-size:15px;color:#059669;" class="rep-code">Rp {{ number_format($totPaySum, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="rep-empty">
                        <svg class="rep-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        <div class="rep-empty-heading">Belum Ada Riwayat Pencairan</div>
                        <div class="rep-empty-desc">Tidak ada data pembayaran yang sesuai dengan filter tanggal atau pencarian.</div>
                    </div>
                @endif

            @endif

        </div>{{-- /rep-card --}}

    </div>{{-- /rep-wrap --}}
</x-filament-panels::page>
