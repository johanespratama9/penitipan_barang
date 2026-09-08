# Panduan Penggunaan Sistem Penitipan Barang (Consignment + POS)

Aplikasi berbasis **Laravel + Filament** untuk manajemen penitipan barang, POS/kasir, pembagian komisi, dan pembayaran penitip.

---

## 1. Status Pengerjaan

Saat ini aplikasi telah menyelesaikan:
* ✅ **STEP 1–5**: Setup Laravel & Filament Panel
* ✅ **STEP 6**: Role & Permission (Spatie Permission + Filament Shield)
* ✅ **STEP 7**: Master Data **Category (Kategori)**
* ✅ **STEP 8**: Master Data **Consignor (Penitip)**
* ✅ **STEP 9**: Master Data **Product (Barang Penitipan)**

---

## 2. Cara Menjalankan Aplikasi

Jalankan perintah berikut pada terminal di folder project:

```bash
# 1. Jalankan development server
php artisan serve
```

Aplikasi akan berjalan di:
* **URL Admin Panel:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
* **URL Landing Page:** [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 3. Akun Login (Default Testing)

Database sudah dilengkapi seeder akun untuk masing-masing role:

| Role | Email | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@penitipan.test` | `password` | Akses penuh CRUD ke semua modul & pengaturan |
| **Kasir** | `kasir@penitipan.test` | `password` | Hanya melihat (read-only) data penitip & produk, akses POS |
| **Penitip** | `penitip@penitipan.test` | `password` | Hanya data profil, barang, dan saldo milik sendiri |

---

## 4. Panduan Penggunaan Modul Kategori (STEP 7)

Akses: Grup **Master Data** > menu **Kategori**.
* **Admin:** Tambah, ubah nama (auto-slug), deskripsi, toggle status aktif, dan hapus kategori.
* **Kasir:** Hanya dapat melihat daftar kategori.
* **Penitip:** Tidak memiliki akses.

---

## 5. Panduan Penggunaan Modul Penitip (STEP 8)

Akses: Grup **Master Data** > menu **Penitip**.
* **Admin:** Pendaftaran penitip baru (kode otomatis `PEN-00001`), hubungkan akun user login, kontak, NIK, alamat, status aktif/ditangguhkan.
* **Kasir:** Melihat kontak & data seluruh penitip.
* **Penitip:** Hanya melihat profil miliknya sendiri.

---

## 6. Panduan Penggunaan Modul Produk / Barang (STEP 9)

### Akses Menu
1. Buka grup **Master Data** pada sidebar kiri > klik menu **Produk / Barang**.
2. URL langsung: [http://127.0.0.1:8000/admin/products](http://127.0.0.1:8000/admin/products)

### Aksi yang Dapat Dilakukan (Berdasarkan Role)

#### A. Sebagai Admin (`admin@penitipan.test`)
* **Tambah Produk Baru:**
  1. Klik tombol **New Produk**.
  2. **Pilih Kategori & Penitip:** Tentukan kategori dan penitip pemilik barang.
  3. **Nama & Kode:** Nama barang wajib diisi. Kode unik terisi otomatis (`PRD-00001`, `PRD-00002`).
  4. **Barcode:** Opsional, dapat discan via barcode scanner fisik untuk POS.
  5. **Harga & Komisi (Reaktif):**
     * Masukkan **Harga Jual (Rp)**.
     * Pilih **Tipe Komisi Toko** (`Persentase %` atau `Nominal Tetap Rp`).
     * Masukkan **Nilai Komisi Toko** (default 20%).
     * Kolom **Pendapatan Penitip (Rp)** akan dihitung otomatis saat itu juga melalui `CommissionService`.
  6. **Stok & Kondisi:** Tentukan stok (default 1), kondisi (Baru, Like New, Bekas Baik, Cukup), dan status barang (`Tersedia`, `Pending`, dsb.).
  7. **Foto & Deskripsi:** Unggah foto barang (otomatis tersimpan di storage lokal `products`).
  8. Klik **Create**.
* **Edit & Hapus Produk:** Admin dapat memperbarui stok, harga, maupun menghapus barang.

#### B. Sebagai Kasir (`kasir@penitipan.test`)
* Kasir dapat melihat daftar seluruh produk, harga jual, foto, barcode, dan sisa stok.
* Kasir dapat membuka detail produk (tombol **View**).
* Kasir **tidak dapat** menambah, mengubah harga, ataupun menghapus barang.

#### C. Sebagai Penitip (`penitip@penitipan.test`)
* Penitip **hanya melihat daftar barang yang ia titipkan sendiri**.
* Barang milik penitip lain tidak akan muncul di tabel penitip.
* Penitip dapat memantau status barang miliknya (`Tersedia`, `Pending`, `Terjual`).

---

## 7. Perintah Penting (CLI)

```bash
# Menjalankan migrasi database
php artisan migrate

# Mengisi data awal lengkap (roles, user, penitip, produk)
php artisan db:seed

# Menjalankan testing modul Produk & Komisi
php artisan test --filter=ProductTest

# Menjalankan seluruh automated test (21 test)
php artisan test
```

---

## 8. Langkah Selanjutnya (Roadmap)

Tahap berikutnya adalah:
* **STEP 10 — Consignment (Penerimaan Barang Penitipan)**
  * Tabel & Model `consignments` dan `consignment_items`
  * Alur penerimaan barang dari penitip (draft -> submitted -> received -> approved/rejected -> completed)
  * Pencatatan riwayat snapshot harga & komisi saat barang diterima.

