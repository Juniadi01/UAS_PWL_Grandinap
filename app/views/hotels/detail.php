<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="<?= BASEURL ?>hotels">Hotel</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e($hotel->name) ?></li>
    </ol>
</nav>

<div class="card overflow-hidden mb-4 reveal">
    <div style="position:relative">
        <img src="<?= photoSrc($hotel->photo, 'hotels', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=1200&auto=format&fit=crop') ?>"
             alt="<?= e($hotel->name) ?>" style="width:100%;height:340px;object-fit:cover;display:block">
        <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(8,16,25,.10),rgba(8,16,25,.78))"></div>
        <div style="position:absolute;left:0;right:0;bottom:0;padding:1.75rem;color:#fff">
            <div class="mb-1"><?= starsHtml($hotel->star) ?></div>
            <h1 class="h3 mb-1 font-display" style="color:#fff"><?= e($hotel->name) ?></h1>
            <p class="mb-0"><i class="bi bi-geo-alt"></i> <?= e($hotel->address ?: $hotel->city) ?><?= $hotel->address ? ', ' . e($hotel->city) : '' ?></p>
        </div>
    </div>
    <?php if (!empty($hotel->description)): ?>
    <div class="card-body">
        <p class="mb-0 text-secondary"><?= nl2br(e($hotel->description)) ?></p>
    </div>
    <?php endif; ?>
</div>

<div class="d-flex justify-content-between align-items-end mb-3">
    <div>
        <div class="eyebrow">Akomodasi</div>
        <h2 class="section-title mb-0">Pilihan Kamar</h2>
    </div>
    <span class="text-muted small"><?= count($rooms) ?> kamar</span>
</div>

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
                <p class="small text-muted mb-2"><i class="bi bi-people"></i> <?= $room->capacity ?> tamu</p>
                <a href="<?= BASEURL ?>rooms/detail/<?= $room->id ?>" class="btn btn-outline-ink btn-sm mt-auto">Detail &amp; Booking</a>
            </div>
        </article>
    </div>
    <?php endforeach; ?>
    <?php if (empty($rooms)): ?>
        <div class="col-12"><div class="empty-state">Belum ada kamar untuk hotel ini.</div></div>
    <?php endif; ?>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
