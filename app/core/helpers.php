<?php
/**
 * helpers.php
 * Kumpulan fungsi bantu: auth, otorisasi, flash message, upload, escaping.
 */

// Escape output -> cegah XSS
function e($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

// Redirect ke URL relatif terhadap BASEURL
function redirect($path = '') {
    header('Location: ' . BASEURL . ltrim($path, '/'));
    exit;
}

// Data user yang sedang login
function currentUser() {
    return $_SESSION['user'] ?? null;
}
function isLoggedIn() { return isset($_SESSION['user']); }
function isAdmin()    { return isLoggedIn() && $_SESSION['user']['role'] === 'admin'; }

// Gerbang akses: wajib login
function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('warning', 'Silakan login terlebih dahulu.');
        redirect('auth/login');
    }
}

// Gerbang akses: wajib admin (Otorisasi berbasis peran)
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        die('<div style="font-family:sans-serif;text-align:center;margin-top:80px">
             <h1>403</h1><p>Akses ditolak. Halaman ini khusus admin.</p>
             <a href="' . BASEURL . '">Beranda</a></div>');
    }
}

// Flash message (tampil sekali)
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

// Upload file gambar dengan validasi tipe & ukuran
// $folder contoh: 'rooms', 'payments', 'profiles', 'hotels'
function uploadImage($fileInput, $folder, &$error = null) {
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // tidak ada file diunggah
    }

    $file = $_FILES[$fileInput];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Gagal mengunggah file.';
        return false;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        $error = 'Format file harus: ' . implode(', ', $allowed);
        return false;
    }

    if ($file['size'] > 2 * 1024 * 1024) { // 2MB
        $error = 'Ukuran file maksimal 2MB.';
        return false;
    }

    // Validasi bahwa ini benar-benar gambar
    if (@getimagesize($file['tmp_name']) === false) {
        $error = 'File bukan gambar yang valid.';
        return false;
    }

    $newName = $folder . '_' . uniqid() . '.' . $ext;
    $target  = UPLOAD_PATH . $folder . '/' . $newName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        $error = 'Tidak bisa menyimpan file (cek izin folder uploads).';
        return false;
    }

    return $newName;
}

// Hapus file upload jika ada
function deleteUpload($folder, $filename) {
    if ($filename) {
        $path = UPLOAD_PATH . $folder . '/' . $filename;
        if (is_file($path)) @unlink($path);
    }
}

// Format Rupiah
function rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

// Hitung jumlah malam antara dua tanggal
function hitungMalam($checkIn, $checkOut) {
    $in  = new DateTime($checkIn);
    $out = new DateTime($checkOut);
    return (int) $in->diff($out)->days;
}

// Render bintang rating (read-only). $rating 0..5
function starsHtml($rating, $count = null) {
    $rating = (float)$rating;
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5;
    $html = '<span class="stars" aria-label="Rating ' . $rating . ' dari 5">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $full)      $html .= '<i class="bi bi-star-fill"></i>';
        elseif ($i == $full+1 && $half) $html .= '<i class="bi bi-star-half"></i>';
        else                  $html .= '<i class="bi bi-star"></i>';
    }
    $html .= '</span>';
    if ($count !== null) $html .= ' <span class="text-muted small">(' . (int)$count . ')</span>';
    return $html;
}

// Sumber gambar fleksibel: menerima URL penuh (data seed) ATAU nama file upload,
// dengan gambar cadangan bila kosong. Dipakai untuk foto hotel & kamar.
function photoSrc($photo, $folder, $fallback = '') {
    if (empty($photo)) return $fallback;
    if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) return $photo;
    return UPLOAD_URL . $folder . '/' . $photo;
}

// Badge warna untuk status (Bootstrap)
function statusBadge($status) {
    $map = [
        'pending'     => 'secondary', 'confirmed'   => 'info',
        'checked_in'  => 'primary',   'checked_out' => 'success',
        'cancelled'   => 'danger',
        'unpaid'      => 'secondary', 'waiting'     => 'warning',
        'verified'    => 'success',   'rejected'    => 'danger',
        'available'   => 'success',   'maintenance' => 'warning',
    ];
    $color = $map[$status] ?? 'dark';
    return '<span class="badge bg-' . $color . ' text-uppercase">' . e($status) . '</span>';
}
