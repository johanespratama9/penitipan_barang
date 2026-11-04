<x-filament-panels::page>
    <div class="tc-wrapper">

        <!-- ─────────────────────────────────────────────────────────────
             1. PANEL PENGATURAN (CONTROL CENTER)
             Sepenuhnya menggunakan CSS mandiri agar 100% rapi dan solid
        ───────────────────────────────────────────────────────────── -->
        <div class="tc-panel no-print">
            
            <!-- Header Bar -->
            <div class="tc-header">
                <div class="tc-header-left">
                    <div class="tc-header-icon">
                        <svg class="tc-icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="tc-title-row">
                            <h2 class="tc-title">Pengaturan Barcode Meja</h2>
                            <span class="tc-badge-pill">Menu Digital</span>
                        </div>
                        <p class="tc-desc">
                            Atur nomor meja dan cetak kartu barcode / QR Code untuk stand akrilik meja. Pelanggan cukup memindai untuk membuka menu (hanya tampilan menu, tanpa pesan online).
                        </p>
                    </div>
                </div>

                <!-- Tombol Aksi Kanan -->
                <div class="tc-header-actions">
                    <a 
                        href="{{ route('menu.digital') }}" 
                        target="_blank"
                        class="tc-btn tc-btn-secondary"
                    >
                        <svg class="tc-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        <span>Tes Buka Menu</span>
                    </a>

                    <button 
                        type="button" 
                        onclick="window.print()" 
                        class="tc-btn tc-btn-primary"
                    >
                        <svg class="tc-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak Semua Barcode Meja ({{ count($this->tableList) }} Meja)</span>
                    </button>
                </div>
            </div>

            <!-- Formulir Kontrol Grid 2 Kolom -->
            <div class="tc-body-grid">
                
                <!-- Kolom Kiri: Penomoran Meja -->
                <div class="tc-card-box">
                    <div class="tc-card-box-header">
                        <span class="tc-section-title">1. Penomoran Meja</span>
                        
                        <!-- Segmented Switcher Mode -->
                        <div class="tc-segmented-switch">
                            <button 
                                type="button" 
                                wire:click="$set('mode', 'range')"
                                class="tc-switch-btn {{ $mode === 'range' ? 'active' : '' }}"
                            >
                                Urut Nomor
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('mode', 'custom')"
                                class="tc-switch-btn {{ $mode === 'custom' ? 'active' : '' }}"
                            >
                                Custom Meja
                            </button>
                        </div>
                    </div>

                    @if($mode === 'range')
                        <div class="tc-row-2">
                            <div class="tc-field">
                                <label class="tc-label">Dari Meja No.</label>
                                <input 
                                    type="number" 
                                    min="1" 
                                    max="100" 
                                    wire:model.live="startNumber"
                                    class="tc-input tc-input-bold"
                                >
                            </div>
                            <div class="tc-field">
                                <label class="tc-label">Sampai Meja No.</label>
                                <input 
                                    type="number" 
                                    min="1" 
                                    max="100" 
                                    wire:model.live="endNumber"
                                    class="tc-input tc-input-bold"
                                >
                            </div>
                        </div>

                        <div class="tc-row-2">
                            <div class="tc-field">
                                <label class="tc-label">Awalan Teks</label>
                                <input 
                                    type="text" 
                                    wire:model.live="prefix"
                                    placeholder="Contoh: Meja "
                                    class="tc-input"
                                >
                            </div>
                            <div class="tc-field tc-field-checkbox">
                                <label class="tc-checkbox-label">
                                    <input type="checkbox" wire:model.live="padZero" class="tc-checkbox">
                                    <span>Format 2 Digit (01, 02)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Presets Cepat -->
                        <div class="tc-preset-row">
                            <span class="tc-preset-label">Pintasan Cepat:</span>
                            <div class="tc-preset-buttons">
                                <button type="button" wire:click="setPreset(4)" class="tc-preset-btn">1 - 4 Meja</button>
                                <button type="button" wire:click="setPreset(8)" class="tc-preset-btn">1 - 8 Meja</button>
                                <button type="button" wire:click="setPreset(12)" class="tc-preset-btn">1 - 12 Meja</button>
                                <button type="button" wire:click="setPreset(20)" class="tc-preset-btn">1 - 20 Meja</button>
                            </div>
                        </div>
                    @else
                        <div class="tc-field">
                            <label class="tc-label">Daftar Meja (Pisahkan dengan koma atau baris baru)</label>
                            <textarea 
                                rows="3" 
                                wire:model.live="customTables"
                                placeholder="Meja 1, Meja 2, VIP 1, VIP 2, Outdoor A..."
                                class="tc-textarea"
                            ></textarea>
                            <span class="tc-hint">Contoh: Meja 1, Meja 2, VIP 1, Outdoor A</span>
                        </div>
                    @endif
                </div>

                <!-- Kolom Kanan: Identitas Toko & Format Cetak -->
                <div class="tc-card-box">
                    <div class="tc-card-box-header">
                        <span class="tc-section-title">2. Identitas & Format Cetak</span>
                    </div>

                    <div class="tc-row-2">
                        <div class="tc-field">
                            <label class="tc-label">Nama Brand / Kafe</label>
                            <input 
                                type="text" 
                                wire:model.live="storeName"
                                class="tc-input tc-input-bold"
                            >
                        </div>
                        <div class="tc-field">
                            <label class="tc-label">Subjudul Kartu</label>
                            <input 
                                type="text" 
                                wire:model.live="subtitle"
                                class="tc-input"
                            >
                        </div>
                    </div>

                    <div class="tc-row-2">
                        <div class="tc-field">
                            <label class="tc-label">Tema Warna Kartu</label>
                            <select wire:model.live="cardTheme" class="tc-select">
                                <option value="amber">Emas / Amber (Warm Elegant)</option>
                                <option value="mono">Hitam & Putih (Minimalis Kontras)</option>
                            </select>
                        </div>
                        <div class="tc-field">
                            <label class="tc-label">Ukuran Stand Akrilik</label>
                            <select wire:model.live="cardSize" class="tc-select">
                                <option value="medium">Stand Akrilik A6 (4 kartu / A4)</option>
                                <option value="small">Stiker Meja Mini (6 kartu / A4)</option>
                                <option value="large">Tent Card Besar (2 kartu / A4)</option>
                            </select>
                        </div>
                    </div>

                    <div class="tc-field">
                        <label class="tc-label">Catatan Pemesanan (Hanya Tampilan)</label>
                        <input 
                            type="text" 
                            wire:model.live="noticeText"
                            class="tc-input tc-input-sm"
                        >
                    </div>
                </div>

            </div>

        </div>

        <!-- ─────────────────────────────────────────────────────────────
             2. PRATINJAU & AREA CETAK KARTU MEJA
             Elemen #printArea ini adalah yang akan dicetak di kertas
        ───────────────────────────────────────────────────────────── -->
        <div class="tc-preview-section">
            
            <!-- Toolbar Status Preview -->
            <div class="tc-preview-bar no-print">
                <div class="tc-preview-bar-left">
                    <span class="tc-status-dot"></span>
                    <span class="tc-preview-title">
                        Pratinjau Lembar Kartu Meja ({{ count($this->tableList) }} Meja)
                    </span>
                    <span class="tc-preview-tag">Stand Akrilik Meja • Format A4</span>
                </div>
                <div class="tc-preview-bar-right">
                    <span>💡 Klik <strong>"Cetak Semua Barcode Meja"</strong> untuk mencetak</span>
                </div>
            </div>

            <!-- Canvas Lembar Cetak -->
            <div id="printArea" class="tc-canvas">
                
                @php
                    $isAmber = ($cardTheme === 'amber');
                @endphp

                <!-- Grid Kartu Meja -->
                <div class="tc-cards-grid">
                    @foreach($this->tableList as $table)
                        
                        <!-- Satu Unit Kartu Meja -->
                        <div class="tc-card-item {{ $isAmber ? 'theme-amber' : 'theme-mono' }}">
                            
                            <!-- Tanda Garis Potong Atas -->
                            <div class="tc-cut-guide">
                                <svg class="tc-icon-cut" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879a3 3 0 11-4.242-4.242 3 3 0 014.242 0M12 12L9.121 9.121m0 0a3 3 0 10-4.242 4.242 3 3 0 004.242-4.242z"/>
                                </svg>
                                <span>GARIS POTONG</span>
                            </div>

                            <!-- Frame Dalam Kartu -->
                            <div class="tc-card-inner">
                                
                                <!-- Brand Header -->
                                <div class="tc-card-header">
                                    <div class="tc-brand-logo">S</div>
                                    <div class="tc-brand-text">
                                        <div class="tc-brand-title">{{ $storeName }}</div>
                                        <div class="tc-brand-subtitle">{{ $subtitle }}</div>
                                    </div>
                                </div>

                                <div class="tc-divider"></div>

                                <!-- Badge Nomor Meja -->
                                <div class="tc-table-badge">
                                    <svg class="tc-icon-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="tc-table-name">{{ $table['name'] }}</span>
                                </div>

                                <!-- Container QR Code -->
                                <div class="tc-qr-container">
                                    @if(!empty($table['qr_code']))
                                        <img 
                                            src="{{ $table['qr_code'] }}" 
                                            alt="QR {{ $table['name'] }}" 
                                            class="tc-qr-image"
                                            loading="eager"
                                        >
                                    @else
                                        <div class="tc-qr-fallback">QR Tidak Tersedia</div>
                                    @endif
                                </div>

                                <!-- Panduan Pindai Kamera -->
                                <div class="tc-scan-instruction">
                                    <div class="tc-scan-headline">
                                        <svg class="tc-icon-camera" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>Arahkan Kamera HP ke QR Code</span>
                                    </div>
                                    <div class="tc-scan-sub">Buka kamera ponsel untuk melihat daftar menu kami</div>
                                </div>

                                <!-- Notice: Hanya untuk melihat menu -->
                                <div class="tc-notice-box">
                                    <svg class="tc-icon-info" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="tc-notice-text">{{ $noticeText }}</span>
                                </div>

                                <!-- Footer: Barcode Vektor & Serial -->
                                <div class="tc-card-footer">
                                    <div class="tc-barcode-vector">
                                        <svg viewBox="0 0 100 15" fill="currentColor">
                                            <rect x="0" y="0" width="2" height="15"/>
                                            <rect x="4" y="0" width="3" height="15"/>
                                            <rect x="9" y="0" width="1" height="15"/>
                                            <rect x="12" y="0" width="4" height="15"/>
                                            <rect x="18" y="0" width="2" height="15"/>
                                            <rect x="22" y="0" width="3" height="15"/>
                                            <rect x="27" y="0" width="1" height="15"/>
                                            <rect x="31" y="0" width="5" height="15"/>
                                            <rect x="38" y="0" width="2" height="15"/>
                                            <rect x="42" y="0" width="4" height="15"/>
                                            <rect x="48" y="0" width="2" height="15"/>
                                            <rect x="52" y="0" width="3" height="15"/>
                                            <rect x="57" y="0" width="1" height="15"/>
                                            <rect x="60" y="0" width="4" height="15"/>
                                            <rect x="66" y="0" width="2" height="15"/>
                                            <rect x="70" y="0" width="3" height="15"/>
                                            <rect x="75" y="0" width="1" height="15"/>
                                            <rect x="78" y="0" width="5" height="15"/>
                                            <rect x="85" y="0" width="2" height="15"/>
                                            <rect x="89" y="0" width="3" height="15"/>
                                            <rect x="94" y="0" width="2" height="15"/>
                                            <rect x="98" y="0" width="2" height="15"/>
                                        </svg>
                                    </div>
                                    <span class="tc-serial-text">{{ $table['code_text'] }}</span>
                                </div>

                            </div>

                            <!-- Tombol Layar (Tersembunyi saat diprint) -->
                            <div class="tc-card-action no-print">
                                <a 
                                    href="{{ $table['url'] }}" 
                                    target="_blank" 
                                    class="tc-link-action"
                                >
                                    Tes Buka Menu Meja Ini ↗
                                </a>
                            </div>

                        </div>

                    @endforeach
                </div>

            </div>

        </div>

    </div>

    <!-- ─────────────────────────────────────────────────────────────
         CSS EKSPLISIT LENGKAP (100% INDEPENDEN DARI TAILWIND BUNDLER)
    ───────────────────────────────────────────────────────────── -->
    <style>
        /* ── Container Wrapper ── */
        .tc-wrapper {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* ── Panel Pengaturan Atas ── */
        .tc-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        :is(.dark, .dark *) .tc-panel {
            background: #111827;
            border-color: #1f2937;
            color: #f3f4f6;
        }

        /* ── Header Top Row ── */
        .tc-header {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        @media (min-width: 768px) {
            .tc-header {
                flex-direction: row;
                align-items: center;
            }
        }

        :is(.dark, .dark *) .tc-header {
            border-bottom-color: #1f2937;
        }

        .tc-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .tc-header-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .tc-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tc-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            line-height: 1.2;
        }

        :is(.dark, .dark *) .tc-title {
            color: #f8fafc;
        }

        .tc-badge-pill {
            font-size: 10px;
            font-weight: 700;
            background: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 9999px;
        }

        .tc-desc {
            font-size: 12px;
            color: #64748b;
            margin: 3px 0 0 0;
            line-height: 1.4;
        }

        :is(.dark, .dark *) .tc-desc {
            color: #94a3b8;
        }

        /* ── Tombol Header ── */
        .tc-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tc-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            line-height: 1;
            border: none;
        }

        .tc-btn-secondary {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .tc-btn-secondary:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        :is(.dark, .dark *) .tc-btn-secondary {
            background: #1f2937;
            color: #cbd5e1;
            border-color: #374151;
        }

        .tc-btn-primary {
            background: #f59e0b;
            color: #ffffff;
            box-shadow: 0 1px 3px rgba(245, 158, 11, 0.4);
        }

        .tc-btn-primary:hover {
            background: #d97706;
        }

        /* ── Grid 2 Kolom Kontrol ── */
        .tc-body-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            padding-top: 16px;
        }

        @media (min-width: 900px) {
            .tc-body-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .tc-card-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        :is(.dark, .dark *) .tc-card-box {
            background: #172133;
            border-color: #1e293b;
        }

        .tc-card-box-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        :is(.dark, .dark *) .tc-card-box-header {
            border-bottom-color: #243358;
        }

        .tc-section-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
        }

        :is(.dark, .dark *) .tc-section-title {
            color: #cbd5e1;
        }

        /* ── Segmented Switcher ── */
        .tc-segmented-switch {
            display: inline-flex;
            background: #e2e8f0;
            border-radius: 8px;
            padding: 2px;
        }

        :is(.dark, .dark *) .tc-segmented-switch {
            background: #0f172a;
        }

        .tc-switch-btn {
            border: none;
            background: transparent;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .tc-switch-btn.active {
            background: #ffffff;
            color: #d97706;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        :is(.dark, .dark *) .tc-switch-btn.active {
            background: #1e293b;
            color: #f59e0b;
        }

        /* ── Input & Fields ── */
        .tc-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .tc-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .tc-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
        }

        :is(.dark, .dark *) .tc-label {
            color: #94a3b8;
        }

        .tc-input, .tc-select, .tc-textarea {
            width: 100%;
            height: 34px;
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: #0f172a;
            font-size: 12px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.15s ease;
        }

        .tc-textarea {
            height: auto;
            min-height: 60px;
            font-family: ui-monospace, monospace;
        }

        :is(.dark, .dark *) .tc-input,
        :is(.dark, .dark *) .tc-select,
        :is(.dark, .dark *) .tc-textarea {
            background: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        .tc-input:focus, .tc-select:focus, .tc-textarea:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.15);
        }

        .tc-input-bold {
            font-weight: 700;
        }

        .tc-input-sm {
            font-size: 11px;
        }

        .tc-field-checkbox {
            justify-content: flex-end;
            padding-bottom: 8px;
        }

        .tc-checkbox-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
        }

        :is(.dark, .dark *) .tc-checkbox-label {
            color: #cbd5e1;
        }

        .tc-checkbox {
            accent-color: #f59e0b;
            width: 15px;
            height: 15px;
            cursor: pointer;
        }

        /* ── Presets ── */
        .tc-preset-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            padding-top: 4px;
        }

        .tc-preset-label {
            font-size: 10px;
            color: #94a3b8;
            font-weight: 600;
        }

        .tc-preset-buttons {
            display: flex;
            gap: 4px;
        }

        .tc-preset-btn {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            padding: 2px 7px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.1s ease;
        }

        .tc-preset-btn:hover {
            border-color: #f59e0b;
            color: #d97706;
            background: #fffbeb;
        }

        :is(.dark, .dark *) .tc-preset-btn {
            background: #0f172a;
            border-color: #334155;
            color: #cbd5e1;
        }

        .tc-hint {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* ── Area Pratinjau Lembar Cetak ── */
        .tc-preview-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .tc-preview-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            padding: 0 4px;
        }

        .tc-preview-bar-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tc-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            background: #f59e0b;
            display: inline-block;
        }

        .tc-preview-title {
            font-weight: 700;
            color: #334155;
        }

        :is(.dark, .dark *) .tc-preview-title {
            color: #e2e8f0;
        }

        .tc-preview-tag {
            color: #94a3b8;
            font-size: 10px;
        }

        .tc-preview-bar-right {
            color: #64748b;
            font-size: 11px;
        }

        /* ── Canvas Abu-abu di Layar ── */
        .tc-canvas {
            background: #e2e8f0;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            box-sizing: border-box;
        }

        :is(.dark, .dark *) .tc-canvas {
            background: #0b1120;
            border-color: #1e293b;
        }

        /* ── Grid Kartu di Layar ── */
        .tc-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 260px));
            gap: 20px;
            justify-content: center;
        }

        /* ── Unit Kartu Meja (Proporsi Stand Akrilik) ── */
        .tc-card-item {
            position: relative;
            background: #ffffff !important;
            border: 1px dashed #94a3b8;
            border-radius: 16px;
            padding: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
            width: 100%;
            max-width: 260px;
            margin: 0 auto;
        }

        /* Garis Potong Badge */
        .tc-cut-guide {
            position: absolute;
            top: -8px;
            left: 12px;
            background: #ffffff;
            padding: 0 5px;
            font-size: 8px;
            font-weight: 800;
            color: #64748b;
            font-family: ui-monospace, monospace;
            display: flex;
            align-items: center;
            gap: 3px;
            z-index: 2;
        }

        /* Frame Dalam Kartu */
        .tc-card-inner {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 8px 8px 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #ffffff;
            box-sizing: border-box;
            width: 100%;
        }

        .theme-amber .tc-card-inner {
            border-color: #fde68a;
        }

        /* ── Brand Header Kartu ── */
        .tc-card-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
        }

        .tc-brand-logo {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: #f59e0b;
            color: #ffffff;
            font-weight: 900;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .theme-mono .tc-brand-logo {
            background: #0f172a;
        }

        .tc-brand-text {
            text-align: left;
        }

        .tc-brand-title {
            font-size: 11px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .tc-brand-subtitle {
            font-size: 8px;
            font-weight: 700;
            color: #d97706;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 1px;
        }

        .theme-mono .tc-brand-subtitle {
            color: #64748b;
        }

        .tc-divider {
            width: 100%;
            height: 1px;
            background: #f1f5f9;
            margin: 7px 0;
        }

        .theme-amber .tc-divider {
            background: #fef3c7;
        }

        /* ── Badge Nomor Meja ── */
        .tc-table-badge {
            background: #0f172a;
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 8px;
        }

        .theme-amber .tc-table-badge {
            background: #1e293b;
            border: 1px solid rgba(245, 158, 11, 0.4);
        }

        .tc-table-name {
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            line-height: 1;
        }

        /* ── QR Box ── */
        .tc-qr-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 7px;
        }

        .theme-amber .tc-qr-container {
            border-color: #fde68a;
        }

        .tc-qr-image {
            width: 125px;
            height: 125px;
            object-fit: contain;
            display: block;
        }

        .tc-qr-fallback {
            width: 125px;
            height: 125px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #94a3b8;
        }

        /* ── Instruksi Scan ── */
        .tc-scan-instruction {
            text-align: center;
            margin-bottom: 6px;
            width: 100%;
        }

        .tc-scan-headline {
            font-size: 10px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .tc-scan-sub {
            font-size: 8px;
            color: #64748b;
            margin-top: 1px;
            font-weight: 500;
        }

        /* ── Notice Box (Hanya Tampilan) ── */
        .tc-notice-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 4px 6px;
            display: flex;
            align-items: flex-start;
            gap: 4px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 6px;
        }

        .theme-mono .tc-notice-box {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .tc-notice-text {
            font-size: 7.5px;
            font-weight: 700;
            color: #92400e;
            line-height: 1.25;
            text-align: left;
        }

        .theme-mono .tc-notice-text {
            color: #334155;
        }

        /* ── Footer Barcode ── */
        .tc-card-footer {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 5px;
            border-top: 1px dashed #e2e8f0;
            box-sizing: border-box;
        }

        .theme-amber .tc-card-footer {
            border-top-color: #fef3c7;
        }

        .tc-barcode-vector {
            display: flex;
            align-items: center;
        }

        .tc-barcode-vector svg {
            width: 75px;
            height: 12px;
            color: #475569;
        }

        .tc-serial-text {
            font-family: ui-monospace, monospace;
            font-size: 8px;
            font-weight: 700;
            color: #64748b;
        }

        /* ── Tombol Quick Action Layar ── */
        .tc-card-action {
            margin-top: 8px;
            text-align: center;
        }

        .tc-link-action {
            font-size: 10px;
            font-weight: 700;
            color: #d97706;
            text-decoration: none;
        }

        .tc-link-action:hover {
            text-decoration: underline;
        }

        /* ── Ukuran Icon Standar ── */
        .tc-icon-lg { width: 20px; height: 20px; }
        .tc-icon-sm { width: 14px; height: 14px; }
        .tc-icon-pin { width: 13px; height: 13px; color: #f59e0b; }
        .tc-icon-camera { width: 13px; height: 13px; color: #d97706; }
        .tc-icon-info { width: 11px; height: 11px; color: #d97706; flex-shrink: 0; }
        .tc-icon-cut { width: 10px; height: 10px; color: #64748b; }

        /* ─────────────────────────────────────────────────────────────
           PRINT PREVIEW & ATURAN CETAK A4 PRESISI (@media print)
        ───────────────────────────────────────────────────────────── */
        @media print {
            body * {
                visibility: hidden !important;
            }

            #printArea, #printArea * {
                visibility: visible !important;
            }

            #printArea {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: transparent !important;
                border: none !important;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 8mm 6mm;
            }

            .tc-cards-grid {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 6mm !important;
                width: 100% !important;
            }

            .tc-card-item {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                box-shadow: none !important;
                border: 1px dashed #64748b !important;
                background: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                width: 100% !important;
                max-width: none !important;
                margin-bottom: 2mm !important;
            }

            .tc-card-inner {
                border-color: #cbd5e1 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .tc-qr-image {
                width: 120px !important;
                height: 120px !important;
            }

            .tc-table-badge, .tc-cut-guide {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</x-filament-panels::page>
