# Panduan Lengkap Sistem Penitipan Barang (Consignment + POS)

Aplikasi web **Sistem Penitipan Barang (Consignment) & Point of Sale (POS)** berbasis **Laravel + Filament + Livewire + Spatie Permission**.

---

## 1. Status Penyelesaian Proyek (Full 22 Steps)

Seluruh tahapan arsitektur dan fungsionalitas telah selesai dikerjakan 100%:

| Step | Modul / Fitur | Status | Keterangan |
| :---: | :--- | :---: | :--- |
| **1–5** | Setup Laravel, Filament Panel, Auth, DB | ✅ Selesai | Admin panel Filament & koneksi database |
| **6** | Role & Permission (Spatie + Shield) | ✅ Selesai | Role `admin`, `kasir`, `penitip` & permissions |
| **7** | Master Data Category (Kategori) | ✅ Selesai | Auto slug, status aktif, filter |
| **8** | Master Data Consignor (Penitip) | ✅ Selesai | Kode unik `PEN-xxxxx`, kontak, relasi user |
| **9** | Master Data Product (Barang) | ✅ Selesai | Kode unik `PRD-xxxxx`, barcode, foto, komisi reaktif |
| **10** | Consignment & Consignment Items | ✅ Selesai | Alur penitipan barang, repeater items, tombol approval |
| **11** | Stock Management & Movements | ✅ Selesai | Mutasi stok (in, out, adjustment, return) & riwayat |
| **12** | POS / Kasir Interaktif | ✅ Selesai | Scan barcode, cari barang, cart, diskon, pembayaran |
| **13** | Commission Service | ✅ Selesai | Hitung komisi persentase (`%`) & nominal tetap (`Rp`) |
| **14** | Consignor Balance Service | ✅ Selesai | Rekap total penjualan, komisi toko, pendapatan & saldo |
| **15** | Consignor Payment | ✅ Selesai | Pencairan dana penitip, validasi batas saldo, resi |
| **16** | Multi-Role Dashboard | ✅ Selesai | Dashboard Admin, Kasir, & Penitip dengan widgets & grafik |
| **17** | Laporan Bisnis & Export CSV | ✅ Selesai | Laporan Penjualan, Penitip, Produk, Pembayaran & CSV |
| **18** | Formulir Publik (`/titip`) | ✅ Selesai | Halaman publik pengajuan titip barang online |
| **19** | Scheduled Check & Notification | ✅ Selesai | Command `consignments:check-expired` & notifikasi |
| **20** | Automated Testing Suite | ✅ Selesai | **39 Feature & Unit Tests Lulus 100% (138 assertions)** |
| **21** | Role Security Review | ✅ Selesai | Otorisasi ketat pada Query, Policy, Page, & Action |
| **22** | Production Readiness | ✅ Selesai | Siap deploy di Ubuntu / Nginx dengan SQLite / MySQL |

---

## 2. Cara Menjalankan Aplikasi

Jalankan perintah berikut di root folder proyek:

```bash
# Jalankan server lokal
php artisan serve
```

Aplikasi dapat diakses melalui browser:
* **Admin Panel:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
* **Formulir Publik Titip Barang:** [http://127.0.0.1:8000/titip](http://127.0.0.1:8000/titip)

---

## 3. Akun Login Default untuk Pengujian

Database telah dilengkapi akun seeder untuk masing-masing role:

| Role | Email | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@penitipan.test` | `password` | Akses penuh CRUD, approval konsinyasi, pencairan dana, laporan |
| **Kasir** | `kasir@penitipan.test` | `password` | Akses POS Kasir, riwayat penjualan hari ini, read-only master data |
| **Penitip** | `penitip@penitipan.test` | `password` | Hanya melihat barang miliknya, saldo, dan riwayat pembayaran |

---

## 4. Panduan Fitur Utama

### A. POS / Kasir Interaktif (Menu: Penjualan > POS / Kasir)
1. Buka menu **POS / Kasir** pada sidebar kiri (`/admin/pos`).
2. **Scan Barcode Pakai Kamera HP (Mobile Ready):**
   * Klik tombol biru **Scan Kamera HP** di bagian atas.
   * Kamera belakang HP akan terbuka dengan viewfinder target barcode.
   * Arahkan kamera ke barcode/QR produk: ponsel akan **bergetar (haptic feedback)** dan mengeluarkan **bunyi beep**, serta otomatis menambahkan barang ke keranjang!
   * Mendukung juga barcode scanner USB / Bluetooth fisik pada kolom input barcode.
3. **Penggunaan di Layar Ponsel (Mobile Responsive):**
   * Pada layar HP, terdapat tab switcher **[Katalog Produk]** dan **[Keranjang]**.
   * Ketika memilih barang di katalog, muncul **Floating Bar** di bawah layar: `🛒 X item | Rp 150.000` dengan tombol **Lihat Keranjang & Bayar**.
   * Tombol kuantitas `+` dan `-` didesain besar dan ramah sentuhan jari.
   * Filter cepat kategori dapat digeser (swipe horizontal) dengan jari.
4. **Keranjang Transaksi & Pembayaran:**
   * Atur quantity dengan tombol `+` atau `-`.
   * Masukkan nama pelanggan (opsional).
   * Masukkan nominal **Diskon (Rp)** jika ada potongan harga.
   * Pilih metode pembayaran: **Tunai**, **QRIS**, atau **Transfer**.
   * Untuk tunai: masukkan jumlah uang diterima, kembalian akan dihitung otomatis.
   * Klik tombol hijau besar **Bayar Sekarang**.
5. **Dampak Otomatis:**
   * Stok produk otomatis berkurang dan dicatat di `stock_movements`.
   * Saldo hak penitip otomatis bertambah sesuai rumus komisi.
   * Invoice terbit, invoice tersimpan di **Riwayat Penjualan**, dan muncul tombol **Cetak Struk**.

---

### B. Penitipan Barang & Approval (Menu: Consignment > Penitipan Barang)
1. Buka menu **Penitipan Barang** (`/admin/consignments`).
2. **Buat Dokumen Penitipan Baru:**
   * Klik **New Penitipan**.
   * Pilih **Penitip**, tanggal terima, dan batas waktu penitipan (expiry).
   * Pada tabel **Rincian Barang yang Dititipkan**, klik **Add to items**:
     * Masukkan nama barang, kategori, qty, harga jual, tipe komisi (%, Rp), dan nilai komisi. Hak penitip otomatis terhitung reaktif.
   * Klik **Create**. Dokumen berstatus `draft` / `received`.
3. **Persetujuan (Approval):**
   * Pada tabel daftar dokumen, klik tombol hijau **Setujui**.
   * Sistem otomatis membuat record **Product** ke katalog dengan status `available` dan stok sesuai qty. Barang langsung muncul di POS!

---

### C. Keuangan & Pencairan Dana Penitip (Menu: Keuangan > Pembayaran Penitip)
1. Buka menu **Pembayaran Penitip** (`/admin/consignor-payments`).
2. Klik **New Pembayaran**.
3. Pilih **Penitip**. Sistem akan menampilkan teks bantuan berupa **Saldo tersedia yang dapat dicairkan**.
4. Masukkan nominal pencairan (sistem mencegah pembayaran jika melebihi saldo).
5. Pilih metode transfer/tunai dan no. referensi bukti bank.
6. Klik **Create**. Saldo penitip otomatis berkurang secara akurat.

---

### D. Laporan Bisnis & Export CSV (Menu: Laporan > Laporan Bisnis)
1. Buka menu **Laporan Bisnis** (`/admin/reports-page`).
2. Tersedia 4 tab laporan:
   * **Laporan Penjualan:** Rekap invoice, kasir, metode pembayaran, dan omzet per rentang tanggal.
   * **Laporan Penitip & Saldo:** Rekap barang terjual, total omzet, hak penitip, dana dibayar, dan sisa saldo.
   * **Laporan Produk & Stok:** Sisa stok barang, total unit terjual, dan omzet per produk.
   * **Laporan Pencairan Dana:** Riwayat pembayaran transfer ke penitip.
3. Klik tombol hijau **Export CSV** untuk mengunduh laporan ke format spreadsheet/Excel.

---

### E. Formulir Titip Barang Publik (`/titip`)
1. Akses URL publik: [http://127.0.0.1:8000/titip](http://127.0.0.1:8000/titip).
2. Calon penitip mengisi formulir:
   * Nama lengkap, WhatsApp, email, dan alamat domisili.
   * Nama barang, kategori, jumlah unit, estimasi harga jual, foto, dan deskripsi.
3. Klik **Kirim Pengajuan Titip Barang**.
4. Sistem otomatis membuat nomor dokumen tracking `CSG-xxxxx` dan mencatat data penitip baru.
5. Admin dapat memeriksa dan menyetujui pengajuan di menu **Penitipan Barang**.

---

## 5. Perintah CLI Penting

```bash
# Menjalankan seluruh automated test (39 test)
php artisan test

# Menjalankan command cron untuk memeriksa penitipan kedaluwarsa
php artisan consignments:check-expired

# Reset & isi ulang database seeder lengkap
php artisan migrate:fresh --seed

# Membersihkan cache aplikasi
php artisan optimize:clear
```

---

## 6. Struktur Database & Model

```
users ─── (1:1) ─── consignors ─── (1:N) ─── products
                         │                         │
                         ├── (1:N) ── consignments │
                         │             └── items   │
                         │                         ▼
                         ├── (1:1) ── balances ── sales ── sale_items
                         └── (1:N) ── payments
```
Semua transaksi finansial dibungkus dalam `DB::transaction()` untuk menjaga integritas data ACID.
