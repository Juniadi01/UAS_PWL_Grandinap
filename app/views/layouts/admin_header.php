<?php $flash = getFlash(); $u = $_GET['url'] ?? ''; ?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' - ' : '' ?>Admin <?= APPNAME ?></title>
    <script>try{var t=localStorage.getItem('gi-theme')||'light';document.documentElement.setAttribute('data-bs-theme',t);}catch(e){}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASEURL ?>public/assets/css/app.css?v=3" rel="stylesheet">
</head>
<body class="admin-body">
<aside class="gi-sidebar">
    <a class="brand" href="<?= BASEURL ?>dashboard"><i class="bi bi-building-fill-gear" style="color:var(--gi-brass-2)"></i> Grand<span class="dot">Inap</span></a>
    <?php
    $groups = [
        'Utama' => [
            'dashboard'        => ['Dashboard', 'bi-speedometer2'],
        ],
        'Master Data' => [
            'hoteladmin'    => ['Hotel', 'bi-buildings'],
            'roomadmin'     => ['Kamar', 'bi-door-open'],
            'facilityadmin' => ['Fasilitas', 'bi-stars'],
            'useradmin'     => ['Pengguna', 'bi-people'],
        ],
        'Transaksi' => [
            'reservationadmin' => ['Reservasi', 'bi-calendar-check'],
            'paymentadmin'     => ['Pembayaran', 'bi-cash-coin'],
        ],
        'Analitik' => [
            'reportadmin' => ['Laporan', 'bi-graph-up-arrow'],
        ],
    ];
    foreach ($groups as $label => $items): ?>
        <div class="nav-section"><?= $label ?></div>
        <?php foreach ($items as $slug => $m):
            $active = str_starts_with($u, $slug) ? 'active' : ''; ?>
            <a class="side-link <?= $active ?>" href="<?= BASEURL . $slug ?>"><i class="bi <?= $m[1] ?>"></i> <?= $m[0] ?></a>
        <?php endforeach;
    endforeach; ?>
    <div class="nav-section">Lainnya</div>
    <a class="side-link" href="<?= BASEURL ?>" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Lihat Situs</a>
    <a class="side-link" href="<?= BASEURL ?>auth/logout" style="color:#f0a3a3"><i class="bi bi-box-arrow-right"></i> Keluar</a>
</aside>

<div class="admin-content">
    <div class="admin-topbar">
        <div>
            <div class="eyebrow mb-1">Panel Admin</div>
            <h4><?= e($title ?? '') ?></h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="theme-toggle" data-theme-toggle type="button" aria-label="Ganti tema" title="Ganti tema" style="border-color:var(--gi-line);background:var(--gi-surface-2);color:var(--gi-text)">
                <i class="bi bi-moon-stars-fill"></i>
            </button>
            <span class="d-none d-sm-flex align-items-center gap-2">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode(currentUser()['name']) ?>&background=0A1F3A&color=fff&bold=true" width="34" height="34" class="rounded-circle" alt="">
                <span class="small"><?= e(currentUser()['name']) ?><br><span class="badge bg-secondary">admin</span></span>
            </span>
        </div>
    </div>
