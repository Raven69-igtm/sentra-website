# Hasil Pemeriksaan Fitur Superadmin

Tanggal pemeriksaan: 19 November 2026

## Ringkasan Eksekutif

Pemeriksaan mencakup route, controller, model, view, migrasi, dan pengujian fitur Superadmin.

Hasil keseluruhan:

- 25 route Superadmin terdaftar dan dapat dipetakan.
- Sintaks seluruh controller Superadmin dan `routes/web.php` valid.
- Modul Dashboard, Kelola Akun, dan Role & Permission memiliki `11` tes lulus dengan `68` assertion.
- Suite gabungan Superadmin memiliki `29` tes, `209` assertion, `26` lulus, dan `3` gagal.
- Ketiga kegagalan berada pada pengujian Global Search di Audit Aktivitas.

## Lingkup Pengujian

### Modul yang diperiksa

1. Dashboard Superadmin
2. Kelola Akun
3. Role & Permission
4. Profil Superadmin
5. Pengaturan Sistem
6. Informasi Terkini
7. Log/Audit Aktivitas
8. Middleware dan pembatasan route

### Route Superadmin

Route yang terdaftar meliputi:

- Dashboard
- Kelola akun, tambah akun, edit akun, hapus akun, reset password, dan toggle status
- Role dan permission
- Pengaturan sistem
- Informasi terkini
- Profil dan perubahan password
- Audit aktivitas dan detail aktivitas

Tidak ada route Superadmin yang tidak dapat dipetakan pada pemeriksaan route.

## Hasil Pengujian

| Modul | Tes | Status |
|---|---:|---|
| Dashboard | 1 | Lulus |
| Kelola Akun pencarian dan CRUD | 10 | Lulus |
| Role & Permission | 2 | Lulus |
| Audit dan Global Search | 16 tes | 13 lulus, 3 gagal |
| Sintaks controller dan route | Semua | Lulus |

## Temuan

### F-01 - Placeholder dan teks bantuan Audit masih menyebut pencarian IP

Tingkat: Sedang

Lokasi:

- `resources/views/backend/superadmin/audit/index.blade.php:41`
- `resources/views/backend/superadmin/audit/index.blade.php:153`

Masalah:

Placeholder dan teks bantuan masih menyatakan bahwa pencarian mencakup alamat IP, padahal alamat IP telah dihapus dari cakupan Global Search.

Dampak:

Pengguna dapat memasukkan IP tetapi tidak mendapatkan pencocokan yang dijanjikan oleh UI.

Rekomendasi:

- Hapus kata `IP` dari placeholder.
- Hapus alamat IP dari teks bantuan pencarian global.

### F-02 - Detail Audit masih menampilkan alamat IP

Tingkat: Rendah

Lokasi:

- `resources/views/backend/superadmin/audit/show.blade.php:136`

Catatan:

Halaman detail masih menampilkan alamat IP. Ini bukan pencarian/filter, tetapi dapat membingungkan bila kebijakan fitur menganggap semua data IP harus dihilangkan dari audit.

Rekomendasi:

Pastikan keputusan produk:

- Tampilkan IP hanya pada detail? atau
- Hilangkan seluruh tampilan IP dari Audit Superadmin.

### F-03 - Suite Audit Search memiliki tiga kegagalan

Tingkat: Tinggi

Kegagalan:

1. Pencarian tanggal satu digit `9` tidak lolos pengujian.
2. Pencarian referensi/identitas pada tes khusus tanpa IP gagal pada pengujian.
3. Tes satu karakter `v` dan `z` belum konsisten karena fixture tanggal November dapat membuat hasil `v` berlaku sebagai pencarian bulan.

Lokasi:

- `tests/Feature/SuperadminAuditSearchTest.php`

Catatan:

Pencarian `v` menghasilkan aktivitas November secara sah karena bulan November mengandung huruf `v`. Beberapa tes lama perlu disusun ulang agar data uji tidak berada pada bulan yang ikut tercocokkan secara periode.

Rekomendasi:

- Gunakan fixture tanggal yang tidak ambigu.
- Batasi keyword uji dengan `akun_id` atau marker yang unik.
- Pisahkan pengujian pencocokan teks dan pencocokan periode.
- Pastikan suite dijalankan sekali dalam urutan bersih dan tidak bergantung pada 15 data pertama.

### F-04 - Logika pencarian Audit memiliki duplikasi implementasi

Tingkat: Sedang

Lokasi:

- `app/Http/Controllers/Superadmin/SuperadminAuditController.php`
- `app/Support/ActivitySearchService.php`

Masalah:

Controller masih memiliki metode lama `applySearch`, `applyTextSearch`, dan `applyNumericSearch`, sedangkan controller utama sekarang menggunakan `ActivitySearchService`.

Dampak:

Perubahan aturan pencarian dapat diterapkan ke satu jalur tetapi tidak ke jalur lain, sehingga berpotensi regresi atau perilaku berbeda.

Rekomendasi:

- Jadikan `ActivitySearchService` satu-satunya sumber logika pencarian.
- Hapus metode pencarian lama yang tidak dipanggil dari controller.

### F-05 - Kebijakan password tidak konsisten

Tingkat: Sedang

Lokasi:

- `app/Http/Controllers/Superadmin/SuperadminAkunController.php:79`
- `app/Http/Controllers/Superadmin/SuperadminAkunController.php:113`
- `app/Http/Controllers/Superadmin/SuperadminAkunController.php:201`
- `app/Http/Controllers/Superadmin/SuperadminProfileController.php:64`
- `resources/views/backend/superadmin/akun/index.blade.php:298`

Masalah:

- Pembuatan akun: password minimal 6 karakter.
- Edit akun: password minimal 6 karakter.
- Reset password: password minimal 6 karakter.
- Ganti password profil: minimal 8 karakter, wajib huruf besar, huruf kecil, dan angka.

Dampak:

Kebijakan keamanan password berbeda antarfitur.

Rekomendasi:

Gunakan satu aturan password untuk semua fungsi akun.

### F-06 - Perhitungan SLA Dinsos perlu diverifikasi secara domain

Tingkat: Sedang

Lokasi:

- `app/Http/Controllers/Superadmin/SuperadminDashboardController.php:166`

Masalah:

Denominator persentase SLA Dinsos dihitung dari gabungan:

- laporan berstatus `dirujuk`, atau
- kategori Dinsos berstatus `selesai`.

Numerator dihitung dari laporan kategori Dinsos berstatus `selesai`.

Rumus ini dapat menghasilkan interpretasi persentase yang tidak sama dengan "laporan Dinsos yang selesai".

Rekomendasi:

Definisikan rumus bisnis yang pasti, misalnya:

- persentase penyelesaian = laporan selesai / seluruh laporan Dinsos, atau
- persentase rujukan selesai = laporan selesai dari status rujukan / seluruh laporan berstatus rujukan.

### F-07 - Skrip verifikasi Superadmin lama mengacu pada route yang sudah tidak ada

Tingkat: Sedang

Lokasi:

- `tests/superadmin_check.php`

Masalah:

Skrip memeriksa route `superadmin.laporan.index`, sedangkan route tersebut tidak terdaftar.

Dampak:

Skrip verifikasi lama dapat selalu gagal walaupun aplikasi Superadmin berjalan normal.

Rekomendasi:

Hapus route lama atau tambahkan pemeriksaan route yang benar untuk laporan PPA/Dinsos.

### F-08 - Proteksi penghapusan akun hanya memeriksa laporan dan percakapan

Tingkat: Rendah

Lokasi:

- `app/Http/Controllers/Superadmin/SuperadminAkunController.php:153`

Masalah:

Penghapusan akun dicegah jika akun memiliki laporan atau percakapan. Namun data lain yang memakai `akun_id`, seperti notifikasi atau activity log, dapat tetap terhubung atau tetap tersimpan sebagai snapshot.

Rekomendasi:

- Pastikan relasi akun dan semua tabel telah memiliki kebijakan cascade/restrict yang konsisten.
- Verifikasi perilaku `activity_logs.akun_id` bila akun dihapus.

### F-09 - Pengamanan password reset sudah memiliki proteksi superadmin aktif terakhir

Status: Aman

Lokasi:

- `app/Http/Controllers/Superadmin/SuperadminAkunController.php`

Hasil pemeriksaan:

- Tidak dapat menghapus akun sendiri.
- Tidak dapat menonaktifkan akun sendiri.
- Tidak dapat menghapus Superadmin terakhir.
- Tidak dapat menonaktifkan Superadmin aktif terakhir.
- Tidak dapat menurunkan satu-satunya Superadmin aktif melalui edit akun.

### F-10 - Proteksi role sistem dan role yang masih dipakai

Status: Aman

Lokasi:

- `app/Http/Controllers/Superadmin/SuperadminRoleController.php`

Hasil pemeriksaan:

- Role sistem tidak dapat dihapus.
- Role yang masih digunakan oleh akun tidak dapat dihapus.
- Validasi nama role dinormalisasi menjadi slug sebelum cek unique.
- Permission divalidasi berdasarkan data permission yang tersedia.

## Pemeriksaan UI dan Logika

### Dashboard

- Hitungan PPA mencakup laporan mobile `unset` pada pengujian.
- Chart tahunan memvalidasi tahun dari query dan fallback ke tahun berjalan.
- Aktivitas terkini menggabungkan aktivitas sistem dan akun terbaru lalu diurutkan menurut waktu.
- Tidak ditemukan error sintaks pada controller dashboard.

### Kelola Akun

- Pencarian ID, nama, email, alamat, dan telepon tersedia.
- Telepon terenkripsi tetap dicari melalui variasi format.
- Filter status, role, jenis kelamin, dan instansi lulus pengujian.
- Wildcard `%` dan `_` tidak diperlakukan sebagai wildcard pencarian.
- Penghapusan akun terlindungi bila memiliki laporan atau percakapan.

### Role & Permission

- Nama role dinormalisasi sebelum validasi.
- Slug berbeda yang menghasilkan nama final sama ditolak oleh unique.
- Nama role `!!!` ditolak.
- Penyimpanan permission memakai validasi `exists`.

### Profil

- Superadmin dapat mengubah nama, email, dan nomor telepon.
- Role dan status sengaja tidak dapat diubah di profil.
- Password lama wajib diverifikasi sebelum perubahan password.
- Password baru wajib mengikuti aturan kompleksitas profil.

### Pengaturan dan Informasi

- Validasi seluruh field dijalankan sebelum penyimpanan.
- Pengunggahan file dibatasi tipe gambar dan ukuran.
- Cache `system_settings` dapat diperbarui dan dibersihkan.
- Penghapusan file upload lama dilakukan saat gambar diganti/dihapus.
- Tidak ada pengujian terotomatis untuk seluruh kombinasinya saat pemeriksaan ini dilakukan.

### Audit Aktivitas

- Semua pencarian dijalankan pada dataset sebelum pagination.
- Filter manual diterapkan sebagai AND.
- Pencarian teks menggunakan parameter binding/LIKE yang di-escape.
- Alamat IP sudah tidak dicakup oleh pencarian global pada implementasi aktif.
- Masih ada teks UI lama yang menyebut pencarian IP.
- Terdapat duplikasi logika pencarian lama yang berpotensi sulit dipelihara.

## Fitur yang Sudah Terverifikasi Aman

- Middleware `role:superadmin` melindungi seluruh grup route Superadmin.
- Semua controller Superadmin lolos pemeriksaan sintaks PHP.
- Tidak ada `dd()`, `dump()`, atau dummy action pada controller/view Superadmin.
- Route halaman Superadmin terdaftar lengkap.
- Proteksi Superadmin aktif terakhir bekerja.
- Proteksi role sistem dan role yang masih dipakai bekerja.
- Pencarian akun dan wildcard aman.
- Penghapusan akun terproteksi untuk laporan dan percakapan.

## Rekomendasi Prioritas

### Prioritas 1

1. Perbaiki tiga kegagalan pengujian Audit Search.
2. Selesaikan duplikasi logika pencarian controller/service.
3. Hapus teks UI lama yang menyebut pencarian IP.
4. Perjelas/rumuskan ulang SLA Dinsos.

### Prioritas 2

1. Samakan kebijakan password seluruh fitur.
2. Perbarui skrip `tests/superadmin_check.php`.
3. Tambahkan pengujian untuk Profil, Pengaturan, Informasi, dan upload.
4. Verifikasi seluruh relasi foreign key sebelum penghapusan akun.

### Prioritas 3

1. Hapus tampilan IP pada detail audit bila kebijakan mewajibkan IP tidak tersimpan/dibagikan.
2. Tambahkan pengujian role dan permission kustom secara end-to-end.
3. Tambahkan pengujian cache pengaturan setelah update.

