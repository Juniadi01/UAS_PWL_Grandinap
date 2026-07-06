<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
    <div>
        <div class="eyebrow">Tersimpan</div>
        <h2 class="section-title">Kamar Favorit Saya</h2>
    </div>
    <a href="<?= BASEURL ?>rooms" class="btn btn-outline-ink btn-sm"><i class="bi bi-search"></i> Cari lagi</a>
</div>

<div class="row g-4">
    <?php foreach ($rooms as $room): ?>
    <div class="col-sm-6 col-xl-4 col-wish">
        <article class="card room-card h-100">
            <div class="media">
                <span class="badge-type"><?= e($room->type) ?></span>
                <button class="wish-btn is-active" data-wishlist="<?= $room->id ?>" type="button" aria-pressed="true" aria-label="Hapus dari favorit"><i class="bi bi-heart-fill"></i></button>
                <a href="<?= BASEURL ?>rooms/detail/<?= $room->id ?>">
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
                <a href="<?= BASEURL ?>rooms/detail/<?= $room->id ?>" class="btn btn-outline-ink btn-sm mt-auto">Detail &amp; Booking</a>
            </div>
        </article>
    </div>
    <?php endforeach; ?>
    <?php if (empty($rooms)): ?>
        <div class="col-12"><div class="empty-state">
            <i class="bi bi-heart d-block mb-2"></i>
            <p class="mb-2">Belum ada kamar favorit. Tap ikon hati di kamar yang kamu suka.</p>
            <a href="<?= BASEURL ?>rooms" class="btn btn-gold btn-sm">Jelajahi kamar</a>
        </div></div>
    <?php endif; ?>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
