# Bugfix Requirements Document

## Introduction

Fitur setting pada Superadmin di website SENTRA tidak berfungsi dengan benar. Pengaturan yang disimpan melalui halaman Setting Superadmin tidak tercermin pada tampilan web utama, sehingga perubahan seperti nama aplikasi, logo, kontak, dan pengaturan lainnya tidak tampil di frontend.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN superadmin mengubah nilai setting (seperti `app_name`, `instansi_name`, `hotline_number`, dll) melalui form di `/superadmin/settings` THEN system menyimpan data ke database DAN menampilkan pesan sukses TAPI nilai lama tetap tampil di frontend karena cache belum di-refresh

1.2 WHEN superadmin mengupload logo baru melalui form setting THEN system menyimpan path logo baru ke database TAPI logo lama tetap tampil di halaman frontend karena cache tidak di-clear

1.3 WHEN superadmin mengubah `session_lifetime_minutes` THEN perubahan tidak langsung berlaku untuk sesi yang sedang aktif karena nilai hanya dibaca saat aplikasi bootstrap

### Expected Behavior (Correct)

2.1 WHEN superadmin mengubah nilai setting melalui form THEN system SHALL menyimpan data ke database DAN langsung menghapus cache terkait sehingga nilai baru langsung tampil di frontend

2.2 WHEN superadmin mengupload logo baru THEN system SHALL menyimpan logo baru, menghapus logo lama dari storage, DAN menghapus cache `app_logo` sehingga logo baru langsung tampil

2.3 WHEN superadmin mengubah `session_lifetime_minutes` THEN system SHALL memperbarui konfigurasi session secara dinamis tanpa perlu restart aplikasi

### Unchanged Behavior (Regression Prevention)

3.1 WHEN superadmin membuka halaman setting THEN system SHALL CONTINUE TO menampilkan form dengan nilai setting saat ini dari database

3.2 WHEN system membaca setting untuk pertama kali THEN system SHALL CONTINUE TO menggunakan cache untuk performa optimal

3.3 WHEN setting tidak ditemukan di database THEN system SHALL CONTINUE TO mengembalikan nilai default yang telah ditentukan

3.4 WHEN terjadi error saat menyimpan setting THEN system SHALL CONTINUE TO menampilkan pesan error dan tidak mengubah data existing
