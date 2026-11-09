<?php

/*
 * Pembatasan akun berdasarkan role.
 *
 * - Staf Gudang : HANYA akun yang tercantum di bawah ini yang bisa login.
 *                 Staf tidak bisa mendaftar sendiri dan tidak bisa dibuat lewat halaman registrasi.
 *                 Tambah / ubah staf cukup di daftar ini, lalu jalankan: php artisan db:seed
 * - Kepala Gudang & Auditor : boleh login setelah akunnya didaftarkan lewat halaman /register
 *                 (dilindungi kode registrasi pada .env: KODE_REGISTRASI).
 */
return [

    'staf' => [
        'staf' => ['name' => 'Staf Gudang', 'password' => 'stafgudang123'],
    ],

    // Role yang boleh mendaftar sendiri melalui halaman registrasi
    'role_registrasi' => ['kepala_gudang', 'auditor'],

    // Kode rahasia registrasi (isi di .env agar tidak sembarang orang bisa mendaftar)
    'kode_registrasi' => env('KODE_REGISTRASI', 'TANI-MAKMUR-2026'),
];
