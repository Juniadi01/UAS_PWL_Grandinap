<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
    <div>
        <div class="eyebrow">Akun Saya</div>
        <h2 class="section-title">Reservasi Saya</h2>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASEURL ?>wishlist" class="btn btn-outline-ink btn-sm"><i class="bi bi-heart"></i> Favorit</a>
        <a href="<?= BASEURL ?>rooms" class="btn btn-gold btn-sm"><i class="bi bi-plus-circle"></i> Pesan Kamar</a>
    </div>
</div>

<div class="card p-0 overflow-hidden">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr>
        <th class="ps-3">Kamar</th><th>Tanggal</th><th>Malam</th><th>Total</th>
        <th>Status</th><th>Pembayaran</th><th class="text-end pe-3">Aksi</th>
    </tr></thead>
    <tbody>
    <?php foreach ($reservations as $r): ?>
        <tr>
            <td class="ps-3"><strong><?= e($r->room_name) ?></strong><br><small class="text-muted"><?= e($r->hotel_name) ?>, <?= e($r->city) ?></small></td>
            <td class="small"><?= date('d M Y', strtotime($r->check_in)) ?><br>&rarr; <?= date('d M Y', strtotime($r->check_out)) ?></td>
            <td><?= $r->nights ?></td>
            <td class="fw-semibold"><?= rupiah($r->total_price) ?></td>
            <td><?= statusBadge($r->status) ?></td>
            <td><?= $r->payment_status ? statusBadge($r->payment_status) : '-' ?></td>
            <td class="text-end pe-3">
                <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                <?php if (in_array($r->payment_status, ['unpaid','rejected'])): ?>
                    <a href="<?= BASEURL ?>account/payment/<?= $r->id ?>" class="btn btn-sm btn-gold"><i class="bi bi-upload"></i> Bayar</a>
                <?php else: ?>
                    <a href="<?= BASEURL ?>account/payment/<?= $r->id ?>" class="btn btn-sm btn-outline-ink">Pembayaran</a>
                <?php endif; ?>

                <?php if (in_array($r->status, ['confirmed','checked_in','checked_out'])): ?>
                    <a href="<?= BASEURL ?>account/voucher/<?= $r->id ?>" class="btn btn-sm btn-outline-ink" target="_blank" title="E-Voucher"><i class="bi bi-ticket-perforated"></i></a>
                <?php endif; ?>

                <?php if ($r->status === 'checked_out' && empty($r->reviewed)): ?>
                    <button class="btn btn-sm btn-ink" data-bs-toggle="modal" data-bs-target="#review<?= $r->id ?>"><i class="bi bi-star"></i> Ulas</button>
                <?php elseif (!empty($r->reviewed)): ?>
                    <span class="badge bg-success-subtle text-success align-self-center"><i class="bi bi-check"></i> Diulas</span>
                <?php endif; ?>

                <?php if (in_array($r->status, ['pending','confirmed'])): ?>
                    <a href="<?= BASEURL ?>account/cancel/<?= $r->id ?>" class="btn btn-sm btn-outline-danger btn-confirm" data-msg="Batalkan reservasi ini?" aria-label="Batalkan"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($reservations)): ?>
        <tr><td colspan="7"><div class="empty-state"><i class="bi bi-calendar-x d-block mb-2"></i><p class="mb-2">Belum ada reservasi.</p><a href="<?= BASEURL ?>rooms" class="btn btn-gold btn-sm">Cari kamar sekarang</a></div></td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
</div>

<!-- Modal ulasan untuk tiap reservasi checked_out yang belum diulas -->
<?php foreach ($reservations as $r): if ($r->status==='checked_out' && empty($r->reviewed)): ?>
<div class="modal fade" id="review<?= $r->id ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="POST" action="<?= BASEURL ?>account/review/<?= $r->id ?>">
      <div class="modal-header">
        <h5 class="modal-title">Beri Ulasan - <?= e($r->room_name) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <p class="small text-muted">Bagaimana pengalaman menginapmu? Ulasanmu membantu tamu lain.</p>
        <div class="text-center mb-3">
          <div class="star-input">
            <?php for ($s=5; $s>=1; $s--): ?>
              <input type="radio" name="rating" id="r<?= $r->id ?>s<?= $s ?>" value="<?= $s ?>" <?= $s===5?'checked':'' ?>>
              <label for="r<?= $r->id ?>s<?= $s ?>" aria-label="<?= $s ?> bintang"><i class="bi bi-star-fill"></i></label>
            <?php endfor; ?>
          </div>
        </div>
        <textarea name="comment" class="form-control" rows="3" placeholder="Ceritakan pengalamanmu (opsional)"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-ink btn-sm" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-gold btn-sm"><i class="bi bi-send"></i> Kirim Ulasan</button>
      </div>
    </form>
  </div>
</div>
<?php endif; endforeach; ?>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
