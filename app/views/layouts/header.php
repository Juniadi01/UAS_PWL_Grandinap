<?php $flash = getFlash(); $u = $_GET['url'] ?? ''; ?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0A1F3A">
    <title><?= isset($title) ? e($title) . ' - ' : '' ?><?= APPNAME ?></title>
    <meta name="description" content="GrandInap - reservasi hotel mewah bintang 5 dengan pengalaman menginap kelas dunia.">

    <!-- Set tema lebih awal agar tidak berkedip -->
    <script>try{var t=localStorage.getItem('gi-theme')||'light';document.documentElement.setAttribute('data-bs-theme',t);}catch(e){}</script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASEURL ?>public/assets/css/app.css?v=3" rel="stylesheet">
</head>
<body data-page="<?= e(explode('/', $u)[0] ?: 'home') ?>">
<a class="skip-link" href="#main">Lewati ke konten</a>

<nav class="navbar navbar-expand-lg gi-nav sticky-top" aria-label="Navigasi utama">
    <div class="container">
        <a class="navbar-brand" href="<?= BASEURL ?>"><i class="bi bi-building-fill-gear" style="color:var(--gi-brass-2)"></i> Grand<span class="dot">Inap</span></a>
        <button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#nav" aria-label="Buka menu">
            <i class="bi bi-list text-white fs-3"></i>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <li class="nav-item"><a class="nav-link <?= $u===''?'active':'' ?>" href="<?= BASEURL ?>"<?= $u===''?' aria-current="page"':'' ?>>Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($u,'hotels')?'active':'' ?>" href="<?= BASEURL ?>hotels">Hotel</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($u,'rooms')?'active':'' ?>" href="<?= BASEURL ?>rooms">Cari Kamar</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($u,'ongkir')?'active':'' ?>" href="<?= BASEURL ?>ongkir"><i class="bi bi-truck"></i> Cek Ongkir</a></li>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASEURL ?>dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link <?= str_starts_with($u,'wishlist')?'active':'' ?>" href="<?= BASEURL ?>wishlist"><i class="bi bi-heart"></i> Favorit</a></li>
                        <li class="nav-item"><a class="nav-link <?= str_starts_with($u,'account')?'active':'' ?>" href="<?= BASEURL ?>account"><i class="bi bi-journal-text"></i> Reservasi</a></li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-inline-flex align-items-center gap-2" data-bs-toggle="dropdown" href="#" role="button">
                            <img src="<?= (currentUser()['photo'] ?? null) ? UPLOAD_URL.'profiles/'.e(currentUser()['photo']) : 'https://ui-avatars.com/api/?name='.urlencode(currentUser()['name']).'&background=C9A24B&color=0A1F3A&bold=true' ?>"
                                 width="30" height="30" class="rounded-circle" style="object-fit:cover" alt="">
                            <span class="d-none d-lg-inline"><?= e(currentUser()['name']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASEURL ?>account/profile"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASEURL ?>auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASEURL ?>auth/login">Masuk</a></li>
                    <li class="nav-item ms-lg-1"><a class="btn btn-gold btn-sm px-3" href="<?= BASEURL ?>auth/register">Daftar</a></li>
                <?php endif; ?>
                <li class="nav-item ms-lg-2">
                    <button class="theme-toggle" data-theme-toggle type="button" aria-label="Ganti tema terang/gelap" title="Ganti tema">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main id="main" class="container py-4">
