<?php require_once APPROOT . '/views/layouts/header.php'; ?>
</main>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="eyebrow mb-3 reveal in">Hospitality Bintang Lima</div>
        <h1 class="reveal in">Menginap dengan<br>cara yang berbeda.</h1>
        <p class="lead mt-3 reveal in">Temukan kamar terbaik di hotel mewah pilihan GrandInap - pesan dalam hitungan menit, dengan ketersediaan real-time.</p>
        <div class="d-flex gap-2 mt-4 reveal in">
            <a href="<?= BASEURL ?>rooms" class="btn btn-gold btn-lg px-4"><i class="bi bi-search me-1"></i> Cari Kamar</a>
            <a href="#unggulan" class="btn btn-outline-light btn-lg px-4">Lihat Pilihan</a>
        </div>
    </div>
</section>

<main id="main" class="container">
    <!-- BOOKING BAR (signature) -->
    <form class="booking-bar row g-3 align-items-end" action="<?= BASEURL ?>rooms" method="GET" role="search">
        <div class="col-md-3 col-6">
            <label class="form-label">Kota Tujuan</label>
            <select name="city" class="form-select">
                <option value="">Semua Kota</option>
                <?php foreach ($cities as $c): ?><option value="<?= e($c->city) ?>"><?= e($c->city) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 col-6">
            <label class="form-label">Tipe Kamar</label>
            <select name="type" class="form-select">
                <option value="">Semua Tipe</option>
                <option>Deluxe</option><option>Suite</option><option>Presidential Suite</option>
            </select>
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label">Tamu</label>
            <input type="number" name="guests" class="form-control" min="1" placeholder="2">
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label">Maks. Harga</label>
            <input type="number" name="max_price" class="form-control" placeholder="5.000.000">
        </div>
        <div class="col-md-2">
            <button class="btn btn-ink w-100"><i class="bi bi-search me-1"></i> Cari</button>
        </div>
    </form>

    <!-- KAMAR UNGGULAN -->
    <section id="unggulan" class="py-5 mt-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <div class="eyebrow">Pilihan Terbaik</div>
                <h2 class="section-title">Kamar Unggulan</h2>
            </div>
            <a href="<?= BASEURL ?>rooms" class="btn btn-outline-ink btn-sm">Semua kamar <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($rooms as $room): ?>
            <div class="col-sm-6 col-lg-4 reveal">
                <article class="card room-card h-100">
                    <div class="media">
                        <span class="badge-type"><?= e($room->type) ?></span>
                        <button class="wish-btn" data-wishlist="<?= $room->id ?>" type="button" aria-pressed="false" aria-label="Simpan ke favorit"><i class="bi bi-heart"></i></button>
                        <a href="<?= BASEURL ?>rooms/detail/<?= $room->id ?>" aria-label="Lihat <?= e($room->name) ?>">
                            <img src="<?= photoSrc($room->photo, 'rooms', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=800&auto=format&fit=crop') ?>" alt="<?= e($room->name) ?>">
                        </a>
                        <div class="price-tag"><?= rupiah($room->price) ?> <small>/malam</small></div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <h5 class="h6 mb-1"><a class="text-reset" href="<?= BASEURL ?>rooms/detail/<?= $room->id ?>"><?= e($room->name) ?></a></h5>
                            <?php if ($room->review_count > 0): ?><span class="rating-pill"><i class="bi bi-star-fill"></i> <?= $room->avg_rating ?></span><?php endif; ?>
                        </div>
                        <p class="text-muted small mb-3"><i class="bi bi-geo-alt"></i> <?= e($room->hotel_name) ?>, <?= e($room->city) ?></p>
                        <a href="<?= BASEURL ?>rooms/detail/<?= $room->id ?>" class="btn btn-outline-ink btn-sm mt-auto">Lihat Detail</a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
            <?php if (empty($rooms)): ?>
                <div class="col-12"><div class="empty-state"><i class="bi bi-moon-stars d-block mb-2"></i>Belum ada kamar tersedia.</div></div>
            <?php endif; ?>
        </div>
    </section>

    <!-- KEUNGGULAN -->
    <section class="py-4">
        <div class="row g-4 text-center">
            <?php
            $perks = [
                ['bi-lightning-charge', 'Konfirmasi Cepat', 'Cek ketersediaan real-time tanpa menunggu.'],
                ['bi-shield-check', 'Pembayaran Aman', 'Verifikasi transaksi oleh tim kami.'],
                ['bi-stars', 'Hotel Bintang 5', 'Hanya properti terbaik di setiap kota.'],
                ['bi-headset', 'Bantuan 24/7', 'Tim siap membantu kapan pun.'],
            ];
            foreach ($perks as $p): ?>
            <div class="col-6 col-md-3 reveal">
                <div class="display-6 text-brass mb-2"><i class="bi <?= $p[0] ?>"></i></div>
                <h6 class="mb-1"><?= $p[1] ?></h6>
                <p class="small text-muted mb-0"><?= $p[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
