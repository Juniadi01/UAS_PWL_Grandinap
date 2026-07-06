<?php
/**
 * Konfigurasi aplikasi GrandInap.
 * Sesuaikan kredensial database & BASEURL dengan environment kamu (XAMPP/Laragon).
 */

// ---- Database ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'grandinap');
define('DB_USER', 'root');     // default XAMPP: root
define('DB_PASS', '');         // default XAMPP: kosong

// ---- Aplikasi ----
// PENTING: akhiri dengan garis miring "/"
define('BASEURL', 'http://localhost/grandinap/');
define('APPNAME', 'GrandInap');

// Folder upload (relatif terhadap root proyek)
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('UPLOAD_URL', BASEURL . 'public/uploads/');

// ---- RajaOngkir (Integrasi API ongkos kirim) ----
define('RAJAONGKIR_KEY', 'Er4rPft4dbf09c6d85a35678uXsOL9rn');
define('RAJAONGKIR_BASEURL', 'https://rajaongkir.komerce.id/api/v1');

// Tampilkan error saat development (matikan saat produksi)
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');
