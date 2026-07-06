<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="mb-4">
    <div class="eyebrow">Katalog</div>
    <h2 class="section-title">Cari Kamar Impianmu</h2>
</div>
<div class="row g-4">
    <!-- Sidebar filter -->
    <div class="col-lg-3">
        <div class="card p-3 position-sticky" style="top:90px">
            <h6 class="mb-3"><i class="bi bi-funnel text-brass"></i> Filter</h6>
            <form action="<?= BASEURL ?>rooms" method="GET">
                <div class="mb-3"><label class="form-label small">Kata Kunci</label>
                    <input type="text" name="keyword" class="form-control form-control-sm" value="<?= e($filters['keyword']) ?>" placeholder="nama kamar / hotel"></div>
                <div class="mb-3"><label class="form-label small">Kota</label>
                    <select name="city" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <?php foreach ($cities as $c): ?>
                            <option value="<?= e($c->city) ?>" <?= $filters['city']==$c->city?'selected':'' ?>><?= e($c->city) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="mb-3"><label class="form-label small">Tipe Kamar</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <?php foreach (['Deluxe','Suite','Presidential Suite'] as $t): ?>
                            <option <?= $filters['type']==$t?'selected':'' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="mb-3"><label class="form-label small">Minimal Tamu</label>
                    <input type="number" name="guests" class="form-control form-control-sm" min="1" value="<?= e($filters['guests']) ?>"></div>
                <div class="mb-3"><label class="form-label small">Maksimal Harga</label>
                    <input type="number" name="max_price" class="form-control form-control-sm" value="<?= e($filters['max_price']) ?>" placeholder="cth: 5000000"></div>
                <div class="mb-3"><label class="form-label small">Urutkan</label>
                    <select name="sort" class="form-select form-select-sm">
                        <?php
                        $sortOpts = [
                            'price_asc'  => 'Harga: termurah',
                            'price_desc' => 'Harga: termahal',
                            'rating'     => 'Rating tertinggi',
                            'newest'     => 'Terbaru',
                        ];
                        $curSort = $filters['sort'] ?: 'price_asc';
                        foreach ($sortOpts as $val => $label): ?>
                            <option value="<?= $val ?>" <?= $curSort===$val?'selected':'' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <button class="btn btn-ink btn-sm w-100">Terapkan Filter</button>
                <a href="<?= BASEURL ?>rooms" class="btn btn-link btn-sm w-100 mt-1">Atur ulang</a>
            </form>
        </div>
    </div>

    <!-- Hasil -->
    <div class="col-lg-9">
        <p class="text-muted small mb-3"><i class="bi bi-grid"></i> <strong><?= count($rooms) ?></strong> kamar ditemukan</p>
        <div class="row g-4">
            <?php foreach ($rooms as $room): ?>
            <div class="col-sm-6 col-xl-4 reveal">
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
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt"></i> <?= e($room->hotel_name) ?>, <?= e($room->city) ?></p>
                        <p class="small text-muted mb-3"><i class="bi bi-people"></i> <?= $room->capacity ?> tamu</p>
                        <a href="<?= BASEURL ?>rooms/detail/<?= $room->id ?>" class="btn btn-outline-ink btn-sm mt-auto">Detail &amp; Booking</a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
            <?php if (empty($rooms)): ?>
                <div class="col-12"><div class="empty-state">
                    <i class="bi bi-search d-block mb-2"></i>
                    <p class="mb-2">Tidak ada kamar yang cocok dengan filtermu.</p>
                    <a href="<?= BASEURL ?>rooms" class="btn btn-outline-ink btn-sm">Atur ulang filter</a>
                </div></div>
            <?php endif; ?>
        </div>

        <!-- Terakhir dilihat (lokal, JS) -->
        <div id="recentViewed" class="d-none mt-5">
            <div class="eyebrow mb-3">Terakhir Dilihat</div>
            <div class="row g-3" data-recent-grid></div>
        </div>
    </div>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
