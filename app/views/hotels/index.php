<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="mb-4">
    <div class="eyebrow">Akomodasi Pilihan</div>
    <h2 class="section-title">Jelajahi Hotel Mewah</h2>
    <p class="text-muted">Telusuri koleksi hotel bintang lima kami, lalu lihat seluruh kamar yang tersedia di tiap properti.</p>
</div>

<form action="<?= BASEURL ?>hotels" method="GET" class="card p-3 mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-sm-4"><label class="form-label small mb-1">Kota</label>
            <select name="city" class="form-select form-select-sm">
                <option value="">Semua Kota</option>
                <?php foreach ($cities as $c): ?>
                    <option value="<?= e($c->city) ?>" <?= ($filters['city'] ?? '')===$c->city?'selected':'' ?>><?= e($c->city) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-3"><label class="form-label small mb-1">Bintang Minimal</label>
            <select name="star" class="form-select form-select-sm">
                <option value="">Semua</option>
                <?php foreach ([5,4,3] as $s): ?>
                    <option value="<?= $s ?>" <?= (string)($filters['star'] ?? '')===(string)$s?'selected':'' ?>><?= $s ?> bintang ke atas</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-3"><label class="form-label small mb-1">Urutkan</label>
            <select name="sort" class="form-select form-select-sm">
                <?php
                $sortOpts = ['star'=>'Bintang tertinggi','price_asc'=>'Harga termurah','rating'=>'Rating tertinggi','name'=>'Nama (A-Z)'];
                $curSort = ($filters['sort'] ?? '') ?: 'star';
                foreach ($sortOpts as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $curSort===$val?'selected':'' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-2 d-grid"><button class="btn btn-ink btn-sm">Terapkan</button></div>
    </div>
</form>

<div class="row g-4">
    <?php foreach ($hotels as $h): ?>
    <div class="col-sm-6 col-lg-4 reveal">
        <article class="card room-card h-100">
            <div class="media">
                <span class="badge-type"><?= (int)$h->star ?> <i class="bi bi-star-fill"></i></span>
                <a href="<?= BASEURL ?>hotels/detail/<?= $h->id ?>" aria-label="Lihat <?= e($h->name) ?>">
                    <img src="<?= photoSrc($h->photo, 'hotels', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=800&auto=format&fit=crop') ?>" alt="<?= e($h->name) ?>">
                </a>
                <?php if (!empty($h->min_price)): ?>
                <div class="price-tag">Mulai <?= rupiah($h->min_price) ?></div>
                <?php endif; ?>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <h5 class="h6 mb-1"><a class="text-reset" href="<?= BASEURL ?>hotels/detail/<?= $h->id ?>"><?= e($h->name) ?></a></h5>
                    <?php if (!empty($h->avg_rating)): ?><span class="rating-pill"><i class="bi bi-star-fill"></i> <?= $h->avg_rating ?></span><?php endif; ?>
                </div>
                <p class="text-muted small mb-2"><i class="bi bi-geo-alt"></i> <?= e($h->city) ?></p>
                <p class="small text-muted mb-3"><i class="bi bi-door-open"></i> <?= (int)$h->room_count ?> tipe kamar tersedia</p>
                <a href="<?= BASEURL ?>hotels/detail/<?= $h->id ?>" class="btn btn-outline-ink btn-sm mt-auto">Lihat Hotel</a>
            </div>
        </article>
    </div>
    <?php endforeach; ?>
    <?php if (empty($hotels)): ?>
        <div class="col-12"><div class="empty-state">Belum ada data hotel.</div></div>
    <?php endif; ?>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
