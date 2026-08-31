# Sistem Informasi Peminjaman Versi Komplit (100% Fitur Wajib & Nilai Plus)

## Kelengkapan Fitur Berdasarkan Kriteria Tugas:
1. **Fitur 1 (Login & Hak Akses):** Multi-role (Admin & Anggota) dengan kata sandi terenkripsi (BCRYPT).
2. **Fitur 2 (CRUD Data Utama):** Halaman kelola Barang/Buku (Tambah, Lihat, Edit modal, Hapus).
3. **Fitur 3 (CRUD Data Pendukung):** Halaman kelola Anggota/Peminjam terhubung ke tabel User.
4. **Fitur 4 (Transaksi Utama & Otomatisasi):** Pencatatan peminjaman secara otomatis mengurangi stok barang/buku.
5. **Fitur 5 (Penyelesaian Transaksi):** Pengembalian barang otomatis mengembalikan jumlah stok.
6. **Fitur 6 (Dashboard/Laporan):** Ringkasan statistik barang, anggota, dan status transaksi aktif.
7. **Fitur Tambahan (Nilai Plus):** 
   - Perhitungan denda keterlambatan otomatis (Rp 2.000/hari).
   - Fitur pencarian/filter data barang.
   - Fitur cetak laporan transaksi ke format PDF/Print.

## Cara Pemasangan:
1. Ekstrak file zip ini langsung di folder `htdocs` XAMPP Anda.
2. Buka phpMyAdmin, buat database bernama `db_peminjaman_lengkap`.
3. Import file `database.sql` ke dalam database `db_peminjaman_lengkap`.
4. Akses melalui browser: `http://localhost/db_peminjaman_lengkap`

## Akun Demo:
- **Admin:** username `admin` | password `admin123`
- **Anggota:** username `budi` | password `admin123`
